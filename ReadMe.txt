


## ⚙️ Persiapan Sebelum Menjalankan Project

Pastikan perangkat sudah terinstall:

### 1. Node.js

Digunakan untuk menjalankan asset frontend (Vite / NPM)
Download: https://nodejs.org/

---

### 2. Visual Studio Code

Digunakan sebagai code editor
Download: https://code.visualstudio.com/

---

### 3. XAMPP / Laragon

Digunakan sebagai web server dan database MySQL

* XAMPP: https://www.apachefriends.org/index.html
* Laragon: https://laragon.org/

---

### 4. Composer

Digunakan untuk menginstall dependency Laravel
Download: https://getcomposer.org/

---

## 🚀 Langkah Instalasi Project



### 1. Install Dependency Laravel

```bash
composer install
```

---

### 2. Install Dependency Frontend

```bash
npm install
```

---

### 3. Migrasi Database

```bash
php artisan migrate

Notif Create Database?  Pilih Yes

```

---

### 8. Seeder Data

```bash
php artisan db:seed
```

## LANGKAH 1 DI LAKUKAN 1 KALI SAJA DALAM 1 DEVICE

---

## ▶️ Menjalankan Aplikasi

### 1. Jalankan Server Laravel

```bash
php artisan serve
```

---

### 2. Jalankan Vite / Frontend

```bash
npm run dev
```

---

## 🌐 Akses Aplikasi

http://127.0.0.1:8000

---

## 👤 Default Login

Gunakan akun berikut untuk mencoba sistem:
http://127.0.0.1:8000/login

### 🔑 Admin

* Email: admin@prodi.local
* Password: admin123

---



## 📌 Catatan


* Pastikan XAMPP/Laragon dalam kondisi aktif
* Jika terjadi error, jalankan ulang:

  * `composer install`
  * `npm install`

---






###############



✨ **Selesai! Project siap digunakan.**
