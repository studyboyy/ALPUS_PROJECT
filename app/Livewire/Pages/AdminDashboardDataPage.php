<?php

namespace App\Livewire\Pages;

use App\Models\AnnualReportSection;
use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Data & Statistik')]
class AdminDashboardDataPage extends Component
{
    public int $tahunDipilih = 0;
    public ?int $tahunBaru = null;
    #[Locked]
    public array $tahunBaruBelumDisimpan = [];

    // ── Tab ──
    public string $activeTab = 'tahunan';

    // ── Data tahunan ──
    public array $statistik = [
        'mahasiswa_aktif'   => 0,
        'ipk'               => 0,
        'dosen_tetap'       => 0,
        'publikasi'         => 0,
        'capaian_mahasiswa' => 0,
        'capaian_lulusan'   => 0,
        'capaian_publikasi' => 0,
        'capaian_kegiatan'  => 0,
    ];

    // ── Data bulanan (inline, tidak perlu sub-component) ──
    public array $bulanan = [];

    public function mount(): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();

        $tahunTerbaru = $this->yearStatQuery()->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }

        $this->loadStatistikForm();
        $this->loadBulananForm();
    }

    // ──────────────────────────────────────────────────
    // Tahun management
    // ──────────────────────────────────────────────────

    public function pilihTahun(int $tahun): void
    {
        $this->tahunDipilih = $tahun;
        $this->loadStatistikForm();
        $this->loadBulananForm();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        if ($tab === 'bulanan') {
            $this->loadBulananForm();
        }
    }

    public function tambahTahun(): void
    {
        $this->validate([
            'tahunBaru' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $tahun = (int) $this->tahunBaru;
        $sudahAda = $this->yearStatQuery()->where('year', $tahun)->exists();
        $this->yearStatQuery()->firstOrCreate(
            ['year' => $tahun],
            [
                'prodi_id' => $this->activeProdiId(),
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 0, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata',   'value' => 0, 'decimals' => 2],
                    ['label' => 'Dosen Tetap',      'value' => 0, 'decimals' => 0],
                    ['label' => 'Publikasi',        'value' => 0, 'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '', 'ipk' => '', 'dosen' => '', 'publikasi' => '',
                    'mahasiswaLastY' => 90, 'ipkLastY' => 90, 'dosenLastY' => 90, 'publikasiLastY' => 90,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif',            'percent' => 0],
                    ['label' => 'Lulusan Tepat Waktu',        'percent' => 0],
                    ['label' => 'Publikasi Ilmiah',           'percent' => 0],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 0],
                ],
            ]
        );

        if (! auth()->user()?->isAdmin() && ! $sudahAda) {
            $this->tahunBaruBelumDisimpan[] = $tahun;
        }
        $this->tahunDipilih = $tahun;
        $this->tahunBaru = null;
        $this->loadStatistikForm();
        $this->loadBulananForm();
        $this->flashStatus('Tahun statistik baru berhasil ditambahkan.');
    }

    public function hapusTahun(int $tahun): void
    {
        if (!auth()->user()?->canDelete()) { $this->flashStatus('Akses hapus hanya untuk Admin.'); return; }
        if ($this->yearStatQuery()->count() <= 1) {
            $this->flashStatus('Minimal satu tahun statistik harus tetap tersedia.');
            return;
        }

        DB::transaction(function () use ($tahun): void {
            $this->monthlyQuery()->where('year', $tahun)->delete();
            DashboardProgramItem::withoutGlobalScopes()
                ->where('prodi_id', $this->activeProdiId())
                ->where('year', $tahun)
                ->delete();
            AnnualReportSection::withoutGlobalScopes()
                ->where('prodi_id', $this->activeProdiId())
                ->where('year', $tahun)
                ->delete();
            $this->yearStatQuery()->where('year', $tahun)->delete();
        });
        $tahunTerbaru = $this->yearStatQuery()->max('year');
        $this->tahunDipilih = is_numeric($tahunTerbaru) ? (int) $tahunTerbaru : $this->tahunDipilih;
        $this->loadStatistikForm();
        $this->loadBulananForm();
        $this->flashStatus('Tahun statistik berhasil dihapus.');
    }

    // ──────────────────────────────────────────────────
    // Tab: Tahunan
    // ──────────────────────────────────────────────────

    public function simpanStatistik(): void
    {
        $bolehSimpan = auth()->user()?->isAdmin()
            || in_array($this->tahunDipilih, $this->tahunBaruBelumDisimpan, true);
        if (! $bolehSimpan) {
            $this->flashStatus('Mengubah statistik tahunan hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->validate([
            'statistik.mahasiswa_aktif' => ['required', 'numeric', 'min:0'],
            'statistik.ipk'             => ['required', 'numeric', 'between:0,4'],
            'statistik.dosen_tetap'     => ['required', 'numeric', 'min:0'],
            'statistik.publikasi'       => ['required', 'numeric', 'min:0'],
            'statistik.capaian_mahasiswa' => ['required', 'numeric', 'between:0,100'],
            'statistik.capaian_lulusan'   => ['required', 'numeric', 'between:0,100'],
            'statistik.capaian_publikasi' => ['required', 'numeric', 'between:0,100'],
            'statistik.capaian_kegiatan'  => ['required', 'numeric', 'between:0,100'],
        ]);

        $this->yearStatQuery()->updateOrCreate(
            ['year' => $this->tahunDipilih],
            [
                'prodi_id' => $this->activeProdiId(),
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => (float) $this->statistik['mahasiswa_aktif'], 'decimals' => 0],
                    ['label' => 'IPK Rata-rata',   'value' => (float) $this->statistik['ipk'],             'decimals' => 2],
                    ['label' => 'Dosen Tetap',      'value' => (float) $this->statistik['dosen_tetap'],     'decimals' => 0],
                    ['label' => 'Publikasi',        'value' => (float) $this->statistik['publikasi'],       'decimals' => 0],
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif',            'percent' => (float) $this->statistik['capaian_mahasiswa']],
                    ['label' => 'Lulusan Tepat Waktu',        'percent' => (float) $this->statistik['capaian_lulusan']],
                    ['label' => 'Publikasi Ilmiah',           'percent' => (float) $this->statistik['capaian_publikasi']],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => (float) $this->statistik['capaian_kegiatan']],
                ],
            ]
        );

        $this->rebuildTrendFromKpi();
        $this->tahunBaruBelumDisimpan = array_values(array_diff($this->tahunBaruBelumDisimpan, [$this->tahunDipilih]));
        $this->loadStatistikForm();
        $this->flashStatus('Data statistik tahun ' . $this->tahunDipilih . ' berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────
    // Tab: Bulanan
    // ──────────────────────────────────────────────────

    private function loadBulananForm(): void
    {
        $annualKpi = $this->yearStatQuery()->where('year', $this->tahunDipilih)->value('kpi');
        $this->ensureMonthlyYear($this->tahunDipilih, is_array($annualKpi) ? $annualKpi : []);

        $rows = $this->monthlyQuery()
            ->where('year', $this->tahunDipilih)
            ->orderBy('month')
            ->get();

        if ($rows->isEmpty()) {
            $this->bulanan = collect(range(1, 12))
                ->map(fn(int $month) => [
                    'month'          => $month,
                    'month_label'    => DashboardMonthlyStat::monthName($month),
                    'mahasiswa_aktif' => 0,
                    'ipk'            => 0,
                    'dosen_tetap'    => 0,
                    'publikasi'      => 0,
                ])
                ->all();
            return;
        }

        $this->bulanan = $rows->map(fn(DashboardMonthlyStat $row) => [
            'month'          => (int) $row->month,
            'month_label'    => DashboardMonthlyStat::monthName((int) $row->month),
            'mahasiswa_aktif' => (float) data_get($row->kpi, 'mahasiswa_aktif', 0),
            'ipk'            => (float) data_get($row->kpi, 'ipk', 0),
            'dosen_tetap'    => (float) data_get($row->kpi, 'dosen_tetap', 0),
            'publikasi'      => (float) data_get($row->kpi, 'publikasi', 0),
        ])->all();
    }

    public function simpanBulanan(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengubah statistik bulanan hanya dapat dilakukan oleh Admin.');
            return;
        }

        $rules = [];
        foreach (array_keys($this->bulanan) as $index) {
            $rules["bulanan.$index.month"]           = ['required', 'integer', 'between:1,12'];
            $rules["bulanan.$index.mahasiswa_aktif"] = ['required', 'numeric', 'min:1'];
            $rules["bulanan.$index.ipk"]             = ['required', 'numeric', 'between:0,4'];
            $rules["bulanan.$index.dosen_tetap"]     = ['required', 'numeric', 'min:1'];
            $rules["bulanan.$index.publikasi"]       = ['required', 'numeric', 'min:1'];
        }
        $this->validate($rules);

        foreach ($this->bulanan as $row) {
            $this->monthlyQuery()->updateOrCreate(
                ['year' => $this->tahunDipilih, 'month' => (int) data_get($row, 'month')],
                [
                    'prodi_id' => $this->activeProdiId(),
                    'kpi' => [
                        'mahasiswa_aktif' => (float) data_get($row, 'mahasiswa_aktif', 0),
                        'ipk'             => (float) data_get($row, 'ipk', 0),
                        'dosen_tetap'     => (float) data_get($row, 'dosen_tetap', 0),
                        'publikasi'       => (float) data_get($row, 'publikasi', 0),
                    ],
                ]
            );
        }

        $this->loadBulananForm();
        $this->flashStatus('Data bulanan berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────

    private function loadStatistikForm(): void
    {
        $stat = $this->yearStatQuery()->where('year', $this->tahunDipilih)->first();
        if (!$stat) {
            $this->statistik = [
                'mahasiswa_aktif'   => 0, 'ipk' => 0, 'dosen_tetap' => 0, 'publikasi' => 0,
                'capaian_mahasiswa' => 0, 'capaian_lulusan' => 0, 'capaian_publikasi' => 0, 'capaian_kegiatan' => 0,
            ];
            return;
        }

        $this->statistik = [
            'mahasiswa_aktif'   => (float) data_get($stat->kpi, '0.value', 0),
            'ipk'               => (float) data_get($stat->kpi, '1.value', 0),
            'dosen_tetap'       => (float) data_get($stat->kpi, '2.value', 0),
            'publikasi'         => (float) data_get($stat->kpi, '3.value', 0),
            'capaian_mahasiswa' => (float) data_get($stat->capaian, '0.percent', 0),
            'capaian_lulusan'   => (float) data_get($stat->capaian, '1.percent', 0),
            'capaian_publikasi' => (float) data_get($stat->capaian, '2.percent', 0),
            'capaian_kegiatan'  => (float) data_get($stat->capaian, '3.percent', 0),
        ];
    }

    private function rebuildTrendFromKpi(): void
    {
        $allStats = $this->yearStatQuery()->orderBy('year')->get(['id', 'year', 'kpi', 'trend']);

        foreach ($allStats as $index => $stat) {
            $window = $allStats->slice(max(0, $index - 4), min(5, $index + 1));

            [$mPoly, $mLastY] = $this->buildPolyline($window->map(fn($r) => (float) data_get($r->kpi, '0.value', 0))->values()->all());
            [$iPoly, $iLastY] = $this->buildPolyline($window->map(fn($r) => (float) data_get($r->kpi, '1.value', 0))->values()->all());
            [$dPoly, $dLastY] = $this->buildPolyline($window->map(fn($r) => (float) data_get($r->kpi, '2.value', 0))->values()->all());
            [$pPoly, $pLastY] = $this->buildPolyline($window->map(fn($r) => (float) data_get($r->kpi, '3.value', 0))->values()->all());

            $this->yearStatQuery()->whereKey(data_get($stat, 'id'))->update([
                'trend' => [
                    'mahasiswa' => $mPoly, 'ipk' => $iPoly, 'dosen' => $dPoly, 'publikasi' => $pPoly,
                    'mahasiswaLastY' => $mLastY, 'ipkLastY' => $iLastY, 'dosenLastY' => $dLastY, 'publikasiLastY' => $pLastY,
                ],
            ]);
        }
    }

    private function buildPolyline(array $values): array
    {
        $values = array_values(array_map(fn($v) => (float) $v, $values));
        $count = count($values);
        if ($count === 0) return ['10,90 300,90', 90.0];
        if ($count === 1) return ['300,90', 90.0];

        $xStart = 10; $xEnd = 300; $yTop = 28; $yBottom = 140;
        $min = min($values); $max = max($values);
        $xStep = ($xEnd - $xStart) / ($count - 1);
        $points = []; $lastY = 90.0;

        foreach ($values as $idx => $value) {
            $x = round($xStart + ($idx * $xStep), 1);
            $y = $max === $min ? 90.0 : $yBottom - (($value - $min) / ($max - $min)) * ($yBottom - $yTop);
            $y = max($yTop, min($yBottom, round($y, 1)));
            $lastY = $y;
            $points[] = $x . ',' . $y;
        }

        return [implode(' ', $points), $lastY];
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    private function activeProdiId(): ?int
    {
        $user = auth()->user();
        if ($user?->isAdmin()) {
            return (int) (session('admin_prodi_id')
                ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id'));
        }

        return $user?->prodi_id ? (int) $user->prodi_id : null;
    }

    private function yearStatQuery()
    {
        $query = DashboardYearStat::query();
        $prodiId = $this->activeProdiId();

        if ($prodiId) {
            $query->withoutGlobalScope('prodi')->where('dashboard_year_stats.prodi_id', $prodiId);
        }

        return $query;
    }

    private function monthlyQuery()
    {
        $query = DashboardMonthlyStat::query();
        $prodiId = $this->activeProdiId();

        if ($prodiId) {
            $query->withoutGlobalScope('prodi')->where('dashboard_monthly_stats.prodi_id', $prodiId);
        }

        return $query;
    }

    private function ensureMonthlyYear(int $year, array $annualKpi): void
    {
        for ($month = 1; $month <= 12; $month++) {
            $this->monthlyQuery()->firstOrCreate(
                ['year' => $year, 'month' => $month],
                [
                    'prodi_id' => $this->activeProdiId(),
                    'kpi' => DashboardMonthlyStat::buildDefaultMonthlyKpi($month, $annualKpi),
                ],
            );
        }
    }

    public function render()
    {
        return view('livewire.pages.admin-dashboard-data-page', [
            'daftarTahun'  => $this->yearStatQuery()->orderByDesc('year')->pluck('year')->all(),
            'bulanSekarang' => (int) now()->format('n'),
        ]);
    }
}
