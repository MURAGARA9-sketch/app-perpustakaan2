# App Perpustakaan — Sistem Perpustakaan Digital Kampus

Aplikasi web berbasis **Laravel 12** untuk membantu Admin/Petugas perpustakaan kampus mengelola
data **kategori buku**, **buku**, **anggota**, serta **transaksi peminjaman & pengembalian buku**.

Project ini dibangun bertahap mengikuti modul ajar Pemrograman Framework (Pertemuan 1–11).

## Fitur

- Login/logout petugas (session-based) dengan proteksi route lewat middleware `auth`
- Hak akses per role: menu **Kategori** khusus admin (middleware `CheckAdminRole`)
- CRUD Kategori, Buku, dan Anggota (dengan pencarian nama anggota)
- Transaksi peminjaman multi-buku, pengembalian buku, dan badge status berwarna
- Dashboard statistik dan Laporan Peminjaman yang mengonsumsi REST API internal
- REST API untuk buku, anggota, peminjaman, dan statistik (lihat [API.md](API.md))
- Halaman profil dan ganti password
- Seeder & Factory dengan data bergaya Indonesia

## Prasyarat

- PHP >= 8.2 dengan ekstensi `mbstring`, `pdo_mysql`, `curl`
- Composer
- MySQL / MariaDB
- Git

## Cara Menjalankan Secara Lokal

### 1. Clone dan install dependency

```bash
git clone https://github.com/MURAGARA9-sketch/app-perpustakaan2.git
cd app-perpustakaan2
composer install
```

### 2. Siapkan `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Buat database kosong bernama `db_perpustakaan`, lalu sesuaikan bagian berikut di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

# Base URL server kedua khusus panggilan API internal (Dashboard & Laporan)
INTERNAL_API_URL=http://127.0.0.1:8011

# Data dummy bergaya Indonesia
APP_FAKER_LOCALE=id_ID
```

### 3. Migrate & seed database

```bash
php artisan migrate:fresh --seed
```

Perintah ini membuat seluruh tabel dari nol lalu mengisi data demo: 3 user, 5 kategori, 20 buku, 15 anggota, dan 10 transaksi peminjaman.

### 4. Jalankan **dua** server

Buka dua terminal di folder project:

```bash
# Terminal 1 - server utama untuk browser
php artisan serve --port=8000

# Terminal 2 - server kedua khusus API internal
php artisan serve --port=8011
```

Server kedua wajib menyala karena `php artisan serve` hanya memproses satu request per waktu.
Dashboard dan Laporan Peminjaman memanggil `/api/stats` dan `/api/loans` lewat `Http::get()`,
sehingga kalau ditujukan ke server yang sama akan saling menunggu (deadlock). Tanpa server kedua,
kedua halaman tetap terbuka tetapi menampilkan angka `0` dan tabel kosong.

Buka `http://127.0.0.1:8000` di browser.

## Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | `admin@pens.ac.id` | `password` |
| Petugas | `petugas1@pens.ac.id` | `password` |
| Petugas | `petugas2@pens.ac.id` | `password` |

## Dokumentasi

- [API.md](API.md) — daftar endpoint REST API beserta contoh request/response
- [DEMO.md](DEMO.md) — skenario demo langkah demi langkah

## Pemahaman MVC

**Model** adalah bagian yang mewakili data dan aturan bisnisnya, misalnya data buku dan cara menyimpannya ke database.
**View** adalah bagian yang menampilkan data ke pengguna dalam bentuk halaman HTML (di Laravel memakai Blade).
**Controller** adalah penghubung keduanya: menerima request dari pengguna, meminta/mengolah data lewat Model, lalu memilih View mana yang ditampilkan sebagai response.
