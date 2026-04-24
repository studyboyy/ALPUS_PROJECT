# Rancangan Skema Database (Tahap Backend)

## 1. annual_reports

Menyimpan data laporan tahunan utama.

- id (bigint, pk)
- year (year, unique)
- title (string)
- executive_summary (longText, nullable)
- head_message (longText, nullable)
- pdf_path (string, nullable)
- is_published (boolean, default false)
- published_at (timestamp, nullable)
- created_at, updated_at

## 2. report_sections

Bab/sub-bab pada tiap laporan tahunan.

- id (bigint, pk)
- annual_report_id (fk -> annual_reports.id)
- section_key (string) contoh: academic_performance, research_pkm
- section_title (string)
- content (longText, nullable)
- sort_order (unsignedInteger, default 0)
- created_at, updated_at

## 3. report_indicators

Indikator KPI untuk statistik/tabel.

- id (bigint, pk)
- annual_report_id (fk -> annual_reports.id)
- category (string) contoh: mahasiswa, lulusan, dosen, publikasi
- name (string)
- unit (string, nullable) contoh: %, orang, artikel
- value (decimal(12,2))
- notes (text, nullable)
- created_at, updated_at

## 4. profile_contents

Konten profil program studi.

- id (bigint, pk)
- key (string, unique) contoh: sejarah, visi_misi, struktur_organisasi
- title (string)
- content (longText)
- created_at, updated_at

## 5. documents

Dokumen pendukung yang dapat diunduh.

- id (bigint, pk)
- category (string) contoh: renstra, renop, standar_mutu, notulen
- title (string)
- description (text, nullable)
- file_path (string)
- year (year, nullable)
- is_public (boolean, default true)
- created_at, updated_at

## 6. galleries

Album/kategori galeri.

- id (bigint, pk)
- category (string) contoh: akademik, mahasiswa, pengabdian, kerjasama
- title (string)
- description (text, nullable)
- event_date (date, nullable)
- created_at, updated_at

## 7. gallery_items

Foto pada setiap galeri.

- id (bigint, pk)
- gallery_id (fk -> galleries.id)
- image_path (string)
- caption (string, nullable)
- sort_order (unsignedInteger, default 0)
- created_at, updated_at

## 8. feedback_messages

Pesan dari halaman kontak.

- id (bigint, pk)
- name (string)
- email (string)
- message (longText)
- status (string, default 'new')
- replied_at (timestamp, nullable)
- created_at, updated_at

## Relasi Utama

- annual_reports hasMany report_sections
- annual_reports hasMany report_indicators
- galleries hasMany gallery_items
