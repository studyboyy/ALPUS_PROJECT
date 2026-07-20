# Use Case Diagram

Source diagram: [`01-use-case-diagram.puml`](./01-use-case-diagram.puml)

## Aktor

- **Pengunjung**: mengakses portal publik, memilih program studi, melihat informasi, mengunduh laporan/dokumen, dan mengirim umpan balik.
- **Kaprodi**: pengguna terautentikasi yang melihat dan menambahkan data pada program studinya sendiri; tidak dapat mengubah atau menghapus data lama.
- **Sekprodi**: pengguna terautentikasi dengan ruang lingkup data program studi yang sama, tetapi tanpa hak hapus.
- **Admin**: memilih prodi melalui dropdown header dan memiliki akses penuh untuk menambah, mengubah, dan menghapus data lintas program studi.

## Aturan hak akses yang digambarkan

1. Kaprodi dan Sekprodi dibatasi oleh `prodi_id` akun yang sedang login.
2. Admin memilih prodi aktif melalui `admin_prodi_id` sebelum mengelola data.
3. Operasi ubah dan hapus data tersimpan hanya tersedia untuk Admin; Kaprodi/Sekprodi hanya INSERT data baru.
4. Login membutuhkan Program Studi, username atau email, dan password.
5. Penambahan program studi baru mencakup pembuatan akun Kaprodi dan penyiapan data awal.

## Kesesuaian dengan implementasi

Diagram ini memetakan route portal publik pada `routes/web.php`, panel Livewire di `app/Livewire/Pages`, autentikasi pada `AdminAuthController`, serta pembatasan hak akses pada `EnsureAdminAuthenticated` dan `HasProdiScope`.
