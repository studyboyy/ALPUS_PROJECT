<?php

namespace App\Services;

use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProdiProvisioner
{
    public function cloneStarterData(Prodi $target): void
    {
        $source = Prodi::query()
            ->where('code', '!=', 'ADMIN')
            ->where('id', '!=', $target->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if (! $source) {
            return;
        }

        foreach (['dashboard_year_stats', 'dashboard_program_items', 'document_items', 'profile_sections', 'annual_report_sections', 'dashboard_monthly_stats'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)->where('prodi_id', $source->id)->orderBy('id')->get()->each(function ($row) use ($table, $target): void {
                $payload = (array) $row;
                unset($payload['id']);
                $payload['prodi_id'] = $target->id;
                $payload['created_at'] = now();
                $payload['updated_at'] = now();
                $payload = $this->personalizePayload($table, $payload, $target);
                DB::table($table)->insert($payload);
            });
        }

        if (Schema::hasTable('home_page_settings')) {
            $home = DB::table('home_page_settings')->where('prodi_id', $source->id)->first();
            if ($home) {
                $payload = (array) $home;
                unset($payload['id']);
                $payload['prodi_id'] = $target->id;
                $payload['header_logo_label'] = $target->name;
                $payload['header_title_text'] = 'Laporan Tahunan Program Studi '.$target->name;
                $payload['contact_email'] = 'sekretariat.'.strtolower($target->code).'@unwari.ac.id';
                $payload['contact_address'] = 'Ruang '.$target->code.', Gedung Program Studi Universitas Winaya Mukti';
                $payload['kaprodi_name'] = 'Dr. Kaprodi '.$target->name;
                $payload['kaprodi_quote'] = 'Komitmen pengembangan akademik dan lulusan unggul Program Studi '.$target->name.'.';
                $payload['kaprodi_photo_url'] = 'https://picsum.photos/seed/'.strtolower($target->code).'-kaprodi/400/400';
                $payload['hero_background_url'] = 'https://picsum.photos/seed/'.strtolower($target->code).'-hero-main/1800/900';
                $gallery = json_decode((string) ($payload['gallery_items'] ?? '[]'), true) ?: [];
                foreach ($gallery as $index => &$item) {
                    $item['title'] = ($item['title'] ?? 'Galeri').' '.$target->code;
                    $item['description'] = ($item['description'] ?? '').' Dokumentasi khusus '.$target->name.'.';
                    $item['image_url'] = 'https://picsum.photos/seed/'.strtolower($target->code).'-gallery-'.$index.'/900/600';
                }
                unset($item);
                $payload['gallery_items'] = json_encode($gallery);
                $payload['created_at'] = now();
                $payload['updated_at'] = now();
                DB::table('home_page_settings')->insert($payload);
            }
        }
    }

    private function personalizePayload(string $table, array $payload, Prodi $target): array
    {
        $offset = (abs(crc32($target->code)) % 9) + 3;

        if ($table === 'dashboard_year_stats') {
            $kpi = json_decode((string) $payload['kpi'], true) ?: [];
            foreach ($kpi as $index => &$item) {
                $item['value'] = $index === 1
                    ? round(min(3.95, (float) ($item['value'] ?? 0) + ($offset / 100)), 2)
                    : (float) ($item['value'] ?? 0) + ($offset * ($index + 1));
            }
            unset($item);
            $payload['kpi'] = json_encode($kpi);
        }

        if ($table === 'dashboard_monthly_stats') {
            $kpi = json_decode((string) $payload['kpi'], true) ?: [];
            $kpi['mahasiswa_aktif'] = (float) ($kpi['mahasiswa_aktif'] ?? 0) + $offset;
            $kpi['ipk'] = round(min(3.95, (float) ($kpi['ipk'] ?? 0) + ($offset / 100)), 2);
            $kpi['dosen_tetap'] = (float) ($kpi['dosen_tetap'] ?? 0) + 2;
            $kpi['publikasi'] = (float) ($kpi['publikasi'] ?? 0) + 1;
            $payload['kpi'] = json_encode($kpi);
        }

        if ($table === 'dashboard_program_items') {
            $payload['title'] .= ' '.$target->code;
            $payload['description'] .= ' Program khusus '.$target->name.'.';
        } elseif ($table === 'document_items') {
            $payload['title'] .= ' '.$target->code;
            $payload['description'] = ($payload['description'] ?? '').' Dokumen '.$target->name.'.';
            $payload['file_name'] = $target->code.'-'.($payload['file_name'] ?? 'dokumen.pdf');
        } elseif ($table === 'profile_sections') {
            $payload['summary'] .= ' '.$target->name.'.';
            $payload['full_content'] = ($payload['full_content'] ?? '').' Profil khusus '.$target->name.'.';
        } elseif ($table === 'annual_report_sections') {
            $payload['content'] = ($payload['content'] ?? '').' Laporan khusus '.$target->name.'.';
        }

        return $payload;
    }
}
