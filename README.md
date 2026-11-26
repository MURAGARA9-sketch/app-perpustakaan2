# App Perpustakaan — Sistem Perpustakaan Digital Kampus

Aplikasi web berbasis **Laravel 12** untuk membantu Admin/Petugas perpustakaan kampus mengelola
data **kategori buku**, **buku**, **anggota**, serta **transaksi peminjaman & pengembalian buku**.

Project ini dibangun bertahap mengikuti modul ajar Pemrograman Framework (Pertemuan 1–11),
sehingga sebagian fitur baru akan tersedia di pertemuan-pertemuan berikutnya.

## Tujuan

- Mendigitalkan pencatatan buku, anggota, dan peminjaman di perpustakaan kampus.
- Menjadi media praktik konsep framework: MVC, routing, validasi, Blade, Eloquent, autentikasi, dan REST API.

## Prasyarat

- PHP >= 8.2 (dengan ekstensi `mbstring`, `pdo_mysql`, `curl`)
- Composer
- MySQL / MariaDB
- Node.js & NPM
- Git

## Cara Menjalankan Secara Lokal

```bash
git clone https://github.com/MURAGARA9-sketch/app-perpustakaan2.git
cd app-perpustakaan2
git checkout dev

composer install
cp .env.example .env
php artisan key:generate
```

Buat database kosong bernama `db_perpustakaan`, lalu sesuaikan bagian database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan development server:

```bash
php artisan migrate
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

## Dokumentasi API

Daftar lengkap endpoint REST API beserta contoh request/response ada di [API.md](API.md).

## Pemahaman MVC

**Model** adalah bagian yang mewakili data dan aturan bisnisnya, misalnya data buku dan cara menyimpannya ke database.
**View** adalah bagian yang menampilkan data ke pengguna dalam bentuk halaman HTML (di Laravel memakai Blade).
**Controller** adalah penghubung keduanya: menerima request dari pengguna, meminta/mengolah data lewat Model, lalu memilih View mana yang ditampilkan sebagai response.
