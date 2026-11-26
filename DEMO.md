# Skenario Demo — App Perpustakaan

Panduan ini mengikuti urutan demo UAS. Setiap langkah berisi apa yang dibuka, data yang diisi, dan hasil yang harus terlihat.

## Persiapan (sebelum demo dimulai)

1. Reset database ke kondisi demo yang bersih:
   ```bash
   php artisan migrate:fresh --seed
   ```
2. Nyalakan **dua** server di dua terminal terpisah:
   ```bash
   php artisan serve --port=8000   # server utama (browser)
   php artisan serve --port=8011   # server API internal (Dashboard & Laporan)
   ```
3. Buka browser ke `http://127.0.0.1:8000/login`.
4. Buka Postman dengan base URL `http://127.0.0.1:8000/api` dan header `Accept: application/json`.
5. Akun yang dipakai:
   - Admin: `admin@pens.ac.id` / `password`
   - Petugas: `petugas1@pens.ac.id` / `password`

## 1. Login sebagai admin

1. Di `/login`, isi email `admin@pens.ac.id` dan password `password`, klik **Login**.
2. **Hasil:** diarahkan ke Dashboard dengan pesan "Login berhasil, selamat datang Bambang Sutrisno.". Navbar menampilkan **Bambang Sutrisno (Admin)** dan tombol Logout.
3. *(Opsional)* Coba password salah: muncul pesan "Email atau password salah.".

## 2. Dashboard statistik

1. Tunjukkan tiga kartu: **Total Buku**, **Total Anggota**, **Peminjaman Aktif**. Setelah seeding, angkanya 20 buku dan 15 anggota.
2. **Penjelasan:** angka diambil oleh `DashboardController` lewat `Http::get()` ke `GET /api/stats` (server port 8011), bukan query langsung ke database.

## 3. Tambah kategori baru

1. Klik menu **Kategori** (hanya muncul untuk admin) → **+ Tambah Kategori**.
2. Isi Nama Kategori `Filsafat`, Deskripsi `Buku pemikiran dan filsafat.`, klik **Simpan**.
3. **Hasil:** kembali ke `/categories` dengan pesan sukses hijau, dan "Filsafat" muncul di tabel.

## 4. Tambah buku dengan kategori

1. Klik menu **Buku** → **+ Tambah Buku**.
2. Isi: Judul `Dunia Sophie`, Penulis `Jostein Gaarder`, Penerbit `Mizan`, Tahun Terbit `1996`, Stok `3`, Kategori **Filsafat**. Klik **Simpan**.
3. **Hasil:** pesan sukses, buku muncul di daftar dengan kolom Kategori berisi **Filsafat** (nama, bukan ID).
4. *(Opsional)* Submit form kosong untuk menunjukkan pesan validasi per field.

## 5. Tambah anggota baru

1. Klik menu **Anggota** → **+ Tambah Anggota**.
2. Isi: Nama `Rizky Pratama`, NIM `2310509999`, Email `rizky.pratama@pens.ac.id`, Nomor Telepon `081234567890`, Alamat `Jl. Raya ITS, Surabaya`, Status **Aktif**. Klik **Simpan**.
3. **Hasil:** pesan sukses, anggota muncul di daftar. Gunakan kolom pencarian dengan kata `Rizky` untuk menemukannya.
4. **Validasi unik:** ulangi tambah anggota dengan NIM `2310509999` atau email yang sama. Muncul pesan "NIM sudah terdaftar." / "Email sudah terdaftar.".

## 6. Buat transaksi peminjaman

1. Klik menu **Peminjaman** → **+ Tambah Peminjaman**.
2. Pilih Anggota **Rizky Pratama**, Tanggal Pinjam hari ini, Tanggal Kembali 7 hari dari hari ini, centang buku **Dunia Sophie** dan satu buku lain. Klik **Simpan**.
3. **Hasil:** form tidak punya pilihan petugas. Teks "Petugas pencatat: Bambang Sutrisno" menunjukkan `user_id` terisi otomatis dari akun yang login (`auth()->id()`).

## 7. Daftar peminjaman aktif

1. Di `/loans`, tunjukkan baris transaksi baru: nama anggota, nama petugas, daftar judul buku, dan badge status kuning **Dipinjam**.
2. **Penjelasan:** nama tampil lewat relasi Eloquent yang di-eager load (`Loan::with(['member', 'user', 'loanItems.book'])`), sehingga tidak terjadi N+1 Query.
3. Buka **Detail** anggota Rizky Pratama di menu Anggota: transaksi muncul di tabel Riwayat Peminjaman.

## 8. Proses pengembalian buku

1. Di `/loans`, klik tombol **Kembalikan** pada transaksi Rizky Pratama.
2. **Hasil:** pesan "Buku berhasil dikembalikan.", badge berubah hijau **Dikembalikan**, dan tombol Kembalikan hilang dari baris itu. Halaman **Detail** menampilkan Tanggal Dikembalikan hari ini.
3. *(Alternatif lewat API)* `PUT /api/loans/{id}/kembalikan` di Postman.

## 9. Laporan peminjaman

1. Klik menu **Laporan** atau tombol **Lihat Laporan Peminjaman** di Dashboard.
2. **Hasil:** seluruh transaksi tampil, termasuk transaksi Rizky Pratama dengan status Dikembalikan.
3. **Penjelasan:** `LoanController@report` mengambil data dari `GET /api/loans` lewat HTTP Client, bukan query Eloquent langsung.

## 10. Demo API di Postman

1. `GET /api/books`: response JSON berformat `BookResource` dengan `data`, `links`, dan `meta` (pagination 10 per halaman). Tunjukkan juga filter `GET /api/books?judul=sophie`.
2. `GET /api/stats`: `total_buku`, `total_anggota`, dan `peminjaman_aktif` sesuai data terbaru (buku bertambah 1 dari langkah 4).
3. *(Opsional)* `POST /api/books` dengan body kosong → status `422` beserta pesan error; `GET /api/books/999` → status `404`.

## 11. Otorisasi role (penutup)

1. Logout, lalu login sebagai `petugas1@pens.ac.id` / `password`.
2. **Hasil:** navbar menampilkan **Siti Rahmawati (Petugas)** tanpa menu Kategori.
3. Akses `http://127.0.0.1:8000/categories` langsung lewat URL: muncul halaman **403**, karena route ditolak oleh middleware `CheckAdminRole`.
4. Logout, lalu buka `/books`: diarahkan kembali ke `/login` oleh middleware `auth`.
