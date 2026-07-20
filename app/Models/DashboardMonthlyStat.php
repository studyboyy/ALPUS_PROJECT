<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\Concerns\HasProdiScope;

class DashboardMonthlyStat extends Model
{
    use HasFactory;
    use HasProdiScope;
    protected $fillable = [
        'year',
        'prodi_id',
        'month',
        'kpi',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'kpi' => 'array',
    ];

    public static function ensureYear(int $year, array $annualKpi = []): void
    {
        if (!Schema::hasTable('dashboard_monthly_stats')) {
            return;
        }

        for ($month = 1; $month <= 12; $month++) {
            static::query()->firstOrCreate(
                ['year' => $year, 'month' => $month],
                ['kpi' => static::buildDefaultMonthlyKpi($month, $annualKpi)],
            );
        }
    }

    public static function summarizeYear(int $year, array $annualKpi = []): array
    {
        if (!Schema::hasTable('dashboard_monthly_stats')) {
            return static::emptySummary($year);
        }

        $rows = static::query()
            ->where('year', $year)
            ->orderBy('month')
            ->get();

        if ($rows->isEmpty()) {
            return static::emptySummary($year);
        }

        $lastMonth = $year === (int) now()->format('Y') ? (int) now()->format('n') : 12;

        $window = $rows
            ->filter(fn(self $row) => $row->month <= $lastMonth)
            ->values();

        if ($window->isEmpty()) {
            $window = $rows->take(1)->values();
        }

        $latest = $window->last();
        $monthCount = max(1, $window->count());

        $realisasiMahasiswa = (float) data_get($latest->kpi, 'mahasiswa_aktif', 0);
        $realisasiIpk = (float) $window->avg(fn(self $row) => (float) data_get($row->kpi, 'ipk', 0));
        $realisasiDosen = (float) data_get($latest->kpi, 'dosen_tetap', 0);
        $realisasiPublikasi = (float) $window->sum(fn(self $row) => (float) data_get($row->kpi, 'publikasi', 0));

        $targetMahasiswa = (float) data_get($annualKpi, '0.value', 0);
        $targetIpk = (float) data_get($annualKpi, '1.value', 0);
        $targetDosen = (float) data_get($annualKpi, '2.value', 0);
        $targetPublikasi = (float) data_get($annualKpi, '3.value', 0);

        $items = [
            // Mahasiswa: progress terhadap target tahunan (masuk akal — bisa 80%, 90%, dsb)
            static::buildItem('Mahasiswa Aktif (YTD)', $realisasiMahasiswa, $targetMahasiswa, $targetMahasiswa, 0),

            // IPK: bukan progress %, tapi nilai absolut dengan status berdasarkan range
            // Gunakan target sebagai batas "lulus", tapi cap progress di 100% agar tidak aneh
            static::buildItemIpk('IPK Rata-rata (YTD)', $realisasiIpk, 2),

            // Dosen: progress terhadap formasi/target (jika target = 0, tampilkan absolut saja)
            static::buildItem('Dosen Tetap (YTD)', $realisasiDosen, $targetDosen > 0 ? $targetDosen : $realisasiDosen, $targetDosen > 0 ? $targetDosen : $realisasiDosen, 0),

            // Publikasi: akumulatif, forecast = annualisasi dari rate bulan berjalan
            static::buildItem('Publikasi (YTD)', $realisasiPublikasi, $targetPublikasi, round(($realisasiPublikasi / $monthCount) * 12, 0), 0),
        ];

        return [
            'year' => $year,
            'month' => (int) $latest->month,
            'monthLabel' => static::monthName((int) $latest->month),
            'updatedAt' => optional($latest->updated_at)?->format('d M Y H:i'),
            'items' => $items,
        ];
    }

    public static function monthName(int $month): string
    {
        return match ($month) {
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
            default => '-',
        };
    }

    private static function buildItem(string $label, float $realisasi, float $target, float $forecast, int $decimals): array
    {
        $progress = $target > 0 ? min(100, ($realisasi / $target) * 100) : 0;
        $progress = max(0, round($progress, 1));

        $status = 'danger';
        if ($progress >= 90) {
            $status = 'success';
        } elseif ($progress >= 70) {
            $status = 'warning';
        }

        return [
            'label'    => $label,
            'realisasi' => round($realisasi, $decimals),
            'target'   => round($target, $decimals),
            'forecast' => round($forecast, $decimals),
            'progress' => $progress,
            'decimals' => $decimals,
            'status'   => $status,
        ];
    }

    /**
     * IPK khusus: status berdasarkan nilai absolut, bukan persentase target.
     * Skala: ≥ 3.50 = Unggul (success), 3.00–3.49 = Baik (warning), < 3.00 = Perlu Perhatian (danger)
     * Progress bar menggambarkan posisi IPK dalam skala 2.00–4.00.
     */
    private static function buildItemIpk(string $label, float $ipk, int $decimals): array
    {
        // Progress bar: posisi dalam rentang 2.00–4.00 (skala IPK akademik)
        $progress = round(max(0, min(100, (($ipk - 2.0) / 2.0) * 100)), 1);

        $status = 'danger';
        if ($ipk >= 3.50) {
            $status = 'success';  // Unggul
        } elseif ($ipk >= 3.00) {
            $status = 'warning';  // Baik / Baik Sekali
        }

        return [
            'label'    => $label,
            'realisasi' => round($ipk, $decimals),
            'target'   => 4.00,    // Skala maks IPK
            'forecast' => round($ipk, $decimals), // IPK tidak perlu diannualisasi
            'progress' => $progress,
            'decimals' => $decimals,
            'status'   => $status,
            'is_ipk'   => true,    // Flag untuk blade agar label status tepat
        ];
    }

    public static function buildDefaultMonthlyKpi(int $month, array $annualKpi): array
    {
        $targetMahasiswa = (float) data_get($annualKpi, '0.value', 0);
        $targetIpk       = (float) data_get($annualKpi, '1.value', 0);
        $targetDosen     = (float) data_get($annualKpi, '2.value', 0);
        $targetPublikasi = (float) data_get($annualKpi, '3.value', 0);

        // Kurva musiman akademik — indeks 0=Jan, 11=Des
        $mahasiswaCurve = [0.82, 0.88, 0.91, 0.90, 0.87, 0.84, 0.83, 0.88, 0.94, 0.96, 0.98, 1.00];
        $ipkOffset      = [-0.14, -0.11, -0.08, -0.05, -0.03, -0.01, -0.09, -0.07, -0.05, -0.03, -0.01, 0.00];
        $dosenCurve     = [0.87, 0.89, 0.91, 0.92, 0.93, 0.94, 0.94, 0.95, 0.96, 0.97, 0.98, 1.00];
        $pubWeights     = [0.05, 0.06, 0.09, 0.10, 0.10, 0.11, 0.07, 0.07, 0.09, 0.10, 0.09, 0.07];

        $idx = $month - 1;

        return [
            'mahasiswa_aktif' => max(1, (int) round($targetMahasiswa * $mahasiswaCurve[$idx])),
            'ipk'             => (float) round(max(2.80, $targetIpk + $ipkOffset[$idx]), 2),
            'dosen_tetap'     => max(1, (int) round($targetDosen * $dosenCurve[$idx])),
            'publikasi'       => max(1, (int) round($targetPublikasi * $pubWeights[$idx])),
        ];
    }

    private static function emptySummary(int $year): array
    {
        return [
            'year' => $year,
            'month' => 0,
            'monthLabel' => '-',
            'updatedAt' => null,
            'items' => [
                ['label' => 'Mahasiswa Aktif (YTD)', 'realisasi' => 0, 'target' => 0, 'forecast' => 0, 'progress' => 0, 'decimals' => 0, 'status' => 'danger'],
                ['label' => 'IPK Rata-rata (YTD)', 'realisasi' => 0, 'target' => 0, 'forecast' => 0, 'progress' => 0, 'decimals' => 2, 'status' => 'danger'],
                ['label' => 'Dosen Tetap (YTD)', 'realisasi' => 0, 'target' => 0, 'forecast' => 0, 'progress' => 0, 'decimals' => 0, 'status' => 'danger'],
                ['label' => 'Publikasi (YTD)', 'realisasi' => 0, 'target' => 0, 'forecast' => 0, 'progress' => 0, 'decimals' => 0, 'status' => 'danger'],
            ],
        ];
    }
}
