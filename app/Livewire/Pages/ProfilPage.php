<?php

namespace App\Livewire\Pages;

use App\Models\DashboardYearStat;
use App\Models\HomePageSetting;
use App\Models\ProfileSection;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Profil Program Studi')]
class ProfilPage extends Component
{
    public function render()
    {
        $profileSections = [];
        // Fallback highlight items — semua kosong agar tidak menipu dengan data statis
        $highlightItems = [
            ['label' => 'Akreditasi',       'value' => '-'],
            ['label' => 'Rasio Dosen',      'value' => '-'],
            ['label' => 'Publikasi',        'value' => '-'],
            ['label' => 'Kerja Sama Aktif', 'value' => '-'],
        ];

        if (Schema::hasTable('profile_sections')) {
            ProfileSection::ensureDefaults();
            $profileSections = ProfileSection::allOrdered()
                ->map(fn($s) => [
                    'slug' => $s->slug,
                    'title' => $s->title,
                    'summary' => $s->summary,
                    'color_class' => $s->color_class,
                    'icon_key' => $s->icon_key,
                ])
                ->toArray();
        }

        if (Schema::hasTable('dashboard_year_stats')) {
            DashboardYearStat::ensureDefaults();
            $aktif = DashboardYearStat::query()->orderByDesc('year')->first();

            if ($aktif) {
                $mahasiswa = (float) data_get($aktif->kpi, '0.value', 0);
                $ipk = (float) data_get($aktif->kpi, '1.value', 0);
                $dosen = (float) data_get($aktif->kpi, '2.value', 0);
                $publikasi = (float) data_get($aktif->kpi, '3.value', 0);
                $avgCapaian = collect($aktif->capaian ?? [])->avg('percent') ?? 0;

                $akreditasi = match (true) {
                    $avgCapaian >= 85 && $ipk >= 3.5 && $publikasi >= 70 => 'Unggul',
                    $avgCapaian >= 75 && $ipk >= 3.2 => 'Baik Sekali',
                    default => 'Baik',
                };

                $rasioDosen = $dosen > 0 ? '1 : ' . max(1, (int) round($mahasiswa / $dosen)) : '-';

                $kerjaSamaAktif = collect(HomePageSetting::current()['gallery_items'] ?? [])
                    ->where('category', 'Kerjasama & MoU')
                    ->count();

                $highlightItems = [
                    ['label' => 'Akreditasi', 'value' => $akreditasi],
                    ['label' => 'Rasio Dosen', 'value' => $rasioDosen],
                    ['label' => 'Publikasi ' . $aktif->year, 'value' => number_format($publikasi, 0, ',', '.') . ' artikel'],
                    ['label' => 'Kerja Sama Aktif', 'value' => $kerjaSamaAktif . ' mitra'],
                ];
            }
        }

        return view('livewire.pages.profil-page', [
            'profileSections' => $profileSections,
            'highlightItems' => $highlightItems,
        ]);
    }
}
