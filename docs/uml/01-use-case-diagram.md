# Use Case Diagram

Source diagram: [`01-use-case-diagram.puml`](./01-use-case-diagram.puml)

## Aktor

- **Pengunjung**: mengakses portal publik, memilih program studi, melihat informasi, mengunduh laporan/dokumen, dan mengirim umpan balik.
- **Kaprodi**: pengguna terautentikasi yang mengelola data milik program studinya sendiri.
- **Sekprodi**: pengguna terautentikasi dengan ruang lingkup data program studi yang sama, tetapi tanpa hak hapus.
- **Admin**: memiliki akses penuh, dapat mengelola data lintas program studi, akun pengguna, serta menambah program studi baru.

## Aturan hak akses yang digambarkan

1. Kaprodi dan Sekprodi dibatasi oleh `prodi_id` akun yang sedang login.
2. Admin dapat memilih/mengelola data lintas program studi.
3. Operasi hapus hanya tersedia untuk Admin.
4. Login membutuhkan Program Studi, username atau email, dan password.
5. Penambahan program studi baru mencakup pembuatan akun Kaprodi dan penyiapan data awal.

## Kesesuaian dengan implementasi

Diagram ini memetakan route portal publik pada `routes/web.php`, panel Livewire di `app/Livewire/Pages`, autentikasi pada `AdminAuthController`, serta pembatasan hak akses pada `EnsureAdminAuthenticated` dan `HasProdiScope`.
