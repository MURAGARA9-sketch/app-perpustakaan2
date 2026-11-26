# Dokumentasi REST API — App Perpustakaan

Base URL: `http://127.0.0.1:8000/api` · Semua request memakai header `Accept: application/json` (dan `Content-Type: application/json` untuk request dengan body).

Endpoint bersifat publik (belum memakai token), sesuai cakupan Pertemuan 9. Response `404` menampilkan field `exception` karena `APP_DEBUG=true` di lingkungan lokal; di produksi (`APP_DEBUG=false`) hanya `message` yang dikirim. Contoh response di bawah diambil langsung dari aplikasi yang sedang berjalan; daftar yang panjang dipotong ke satu item.

## Ringkasan Endpoint

| Method | URL | Body (jika ada) | Contoh Response (status + isi) |
|---|---|---|---|
| GET | `/api/books` | - | `200` `{"data": [{"id": 1, "judul": "Astronomi: Menjelajah Alam Semesta", "penulis": "Halima W...` |
| GET | `/api/books?judul=bumi` | - | `200` `{"data": [{"id": 21, "judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "pene...` |
| GET | `/api/books/21` | - | `200` `{"data": {"id": 21, "judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "pener...` |
| GET | `/api/books/999` | - | `404` `{"message": "No query results for model [App\\Models\\Book] 999", "exception": "Symfony...` |
| POST | `/api/books` | `{"judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "penerbit": "Hasta Mitra", "tahun_terbit": 1980, "isbn": "9789794330746", "stok": 4, "category_id": 1}` | `201` `{"data": {"id": 21, "judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "pener...` |
| POST | `/api/books` | `{}` (kosong) | `422` `{"message": "Judul buku wajib diisi. (and 5 more errors)", "errors": {"judul": ["Judul ...` |
| PUT | `/api/books/21` | `{"judul": "Bumi Manusia (Edisi Revisi)", "penulis": "Pramoedya Ananta Toer", "penerbit": "Hasta Mitra", "tahun_terbit": 1980, "isbn": "9789794330746", "stok": 5, "category_id": 1}` | `200` `{"data": {"id": 21, "judul": "Bumi Manusia (Edisi Revisi)", "penulis": "Pramoedya Anant...` |
| GET | `/api/members` | - | `200` `{"data": [{"id": 1, "nama": "Lukita Uyainah", "nim": "3520129226", "email": "lukita.uya...` |
| GET | `/api/members/1` | - | `200` `{"data": {"id": 1, "nama": "Lukita Uyainah", "nim": "3520129226", "email": "lukita.uyai...` |
| GET | `/api/members/999` | - | `404` `{"message": "No query results for model [App\\Models\\Member] 999", "exception": "Symfo...` |
| POST | `/api/loans` | `{"member_id": 1, "user_id": 1, "tanggal_pinjam": "2026-11-26", "tanggal_kembali": "2026-12-03", "book_ids": [21]}` | `201` `{"data": {"id": 11, "member": {"id": 1, "nama": "Lukita Uyainah"}, "petugas": {"id": 1,...` |
| POST | `/api/loans` | `{"member_id": 1}` | `422` `{"message": "Petugas (user_id) wajib diisi. (and 3 more errors)", "errors": {"user_id":...` |
| GET | `/api/loans` | - | `200` `{"data": [{"id": 1, "member": {"id": 4, "nama": "Teddy Palastri"}, "petugas": {"id": 2,...` |
| PUT | `/api/loans/11/kembalikan` | - | `200` `{"data": {"id": 11, "member": {"id": 1, "nama": "Lukita Uyainah"}, "petugas": {"id": 1,...` |
| PUT | `/api/loans/999/kembalikan` | - | `404` `{"message": "No query results for model [App\\Models\\Loan] 999", "exception": "Symfony...` |
| GET | `/api/stats` | - | `200` `{"total_buku": 21, "total_anggota": 15, "peminjaman_aktif": 2}` |
| DELETE | `/api/books/21` | - | `409` `{"message": "Buku tidak bisa dihapus karena tercatat di transaksi peminjaman."}` |
| DELETE | `/api/books/22` | - | `200` `{"message": "Buku berhasil dihapus."}` |
| DELETE | `/api/books/999` | - | `404` `{"message": "No query results for model [App\\Models\\Book] 999", "exception": "Symfony...` |

## Detail Request & Response

### GET `/api/books` — Daftar buku (pagination 10/halaman). Query opsional: `?judul=`, `?category_id=`, `?page=`

Status: **200**

Response:

```json
{
  "data": [
    {
      "id": 1,
      "judul": "Astronomi: Menjelajah Alam Semesta",
      "penulis": "Halima Wijayanti S.IP",
      "penerbit": "Erlangga",
      "tahun_terbit": "1998",
      "isbn": "9791712704799",
      "stok": 1,
      "sampul": null,
      "category": {
        "id": 3,
        "nama_kategori": "Sains"
      },
      "created_at": "2026-11-26T09:48:34.000000Z",
      "updated_at": "2026-11-26T09:48:34.000000Z"
    },
    "... (dipotong)"
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/books?page=1",
    "last": "http://127.0.0.1:8000/api/books?page=3",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/books?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "path": "http://127.0.0.1:8000/api/books",
    "per_page": 10,
    "to": 10,
    "total": 21
  }
}
```

### GET `/api/books?judul=bumi` — Filter judul (LIKE)

Status: **200**

Response:

```json
{
  "data": [
    {
      "id": 21,
      "judul": "Bumi Manusia",
      "penulis": "Pramoedya Ananta Toer",
      "penerbit": "Hasta Mitra",
      "tahun_terbit": "1980",
      "isbn": "9789794330746",
      "stok": 4,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Novel"
      },
      "created_at": "2026-11-26T09:48:35.000000Z",
      "updated_at": "2026-11-26T09:48:35.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/books?page=1",
    "last": "http://127.0.0.1:8000/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8000/api/books",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

### GET `/api/books/21` — Detail satu buku

Status: **200**

Response:

```json
{
  "data": {
    "id": 21,
    "judul": "Bumi Manusia",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": "1980",
    "isbn": "9789794330746",
    "stok": 4,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Novel"
    },
    "created_at": "2026-11-26T09:48:35.000000Z",
    "updated_at": "2026-11-26T09:48:35.000000Z"
  }
}
```

### GET `/api/books/999` — Detail buku yang tidak ada

Status: **404**

Response:

```json
{
  "message": "No query results for model [App\\Models\\Book] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```

### POST `/api/books` — Tambah buku (validasi `StoreBookRequest`)

Status: **201**

Body:

```json
{
  "judul": "Bumi Manusia",
  "penulis": "Pramoedya Ananta Toer",
  "penerbit": "Hasta Mitra",
  "tahun_terbit": 1980,
  "isbn": "9789794330746",
  "stok": 4,
  "category_id": 1
}
```

Response:

```json
{
  "data": {
    "id": 21,
    "judul": "Bumi Manusia",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": 1980,
    "isbn": "9789794330746",
    "stok": 4,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Novel"
    },
    "created_at": "2026-11-26T09:48:35.000000Z",
    "updated_at": "2026-11-26T09:48:35.000000Z"
  }
}
```

### POST `/api/books` — Tambah buku dengan body kosong

Status: **422**

Body:

```json
{}
```

Response:

```json
{
  "message": "Judul buku wajib diisi. (and 5 more errors)",
  "errors": {
    "judul": [
      "Judul buku wajib diisi."
    ],
    "penulis": [
      "Nama penulis wajib diisi."
    ],
    "penerbit": [
      "Nama penerbit wajib diisi."
    ],
    "tahun_terbit": [
      "Tahun terbit wajib diisi."
    ],
    "stok": [
      "Stok wajib diisi."
    ],
    "category_id": [
      "Kategori wajib dipilih."
    ]
  }
}
```

### PUT `/api/books/21` — Ubah buku

Status: **200**

Body:

```json
{
  "judul": "Bumi Manusia (Edisi Revisi)",
  "penulis": "Pramoedya Ananta Toer",
  "penerbit": "Hasta Mitra",
  "tahun_terbit": 1980,
  "isbn": "9789794330746",
  "stok": 5,
  "category_id": 1
}
```

Response:

```json
{
  "data": {
    "id": 21,
    "judul": "Bumi Manusia (Edisi Revisi)",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": 1980,
    "isbn": "9789794330746",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Novel"
    },
    "created_at": "2026-11-26T09:48:35.000000Z",
    "updated_at": "2026-11-26T09:48:35.000000Z"
  }
}
```

### GET `/api/members` — Daftar anggota (pagination 10/halaman)

Status: **200**

Response:

```json
{
  "data": [
    {
      "id": 1,
      "nama": "Lukita Uyainah",
      "nim": "3520129226",
      "email": "lukita.uyainah738@pens.ac.id",
      "nomor_telepon": "085241842432",
      "alamat": "Psr. Acordion No. 662, Tidore Kepulauan 15830, Sumbar",
      "status": "aktif",
      "created_at": "2026-11-26T09:48:34.000000Z",
      "updated_at": "2026-11-26T09:48:34.000000Z"
    },
    "... (dipotong)"
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/members?page=1",
    "last": "http://127.0.0.1:8000/api/members?page=2",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/members?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "http://127.0.0.1:8000/api/members",
    "per_page": 10,
    "to": 10,
    "total": 15
  }
}
```

### GET `/api/members/1` — Detail satu anggota

Status: **200**

Response:

```json
{
  "data": {
    "id": 1,
    "nama": "Lukita Uyainah",
    "nim": "3520129226",
    "email": "lukita.uyainah738@pens.ac.id",
    "nomor_telepon": "085241842432",
    "alamat": "Psr. Acordion No. 662, Tidore Kepulauan 15830, Sumbar",
    "status": "aktif",
    "created_at": "2026-11-26T09:48:34.000000Z",
    "updated_at": "2026-11-26T09:48:34.000000Z"
  }
}
```

### GET `/api/members/999` — Detail anggota yang tidak ada

Status: **404**

Response:

```json
{
  "message": "No query results for model [App\\Models\\Member] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```

### POST `/api/loans` — Buat transaksi peminjaman

Status: **201**

Body:

```json
{
  "member_id": 1,
  "user_id": 1,
  "tanggal_pinjam": "2026-11-26",
  "tanggal_kembali": "2026-12-03",
  "book_ids": [
    21
  ]
}
```

Response:

```json
{
  "data": {
    "id": 11,
    "member": {
      "id": 1,
      "nama": "Lukita Uyainah"
    },
    "petugas": {
      "id": 1,
      "name": "Bambang Sutrisno"
    },
    "tanggal_pinjam": "2026-11-26",
    "tanggal_kembali": "2026-12-03",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      {
        "id": 21,
        "judul": "Bumi Manusia (Edisi Revisi)"
      }
    ]
  }
}
```

### POST `/api/loans` — Buat transaksi dengan data tidak lengkap

Status: **422**

Body:

```json
{
  "member_id": 1
}
```

Response:

```json
{
  "message": "Petugas (user_id) wajib diisi. (and 3 more errors)",
  "errors": {
    "user_id": [
      "Petugas (user_id) wajib diisi."
    ],
    "tanggal_pinjam": [
      "Tanggal pinjam wajib diisi."
    ],
    "tanggal_kembali": [
      "Tanggal kembali wajib diisi."
    ],
    "book_ids": [
      "Pilih minimal satu buku."
    ]
  }
}
```

### GET `/api/loans` — Daftar peminjaman beserta anggota, petugas, dan buku

Status: **200**

Response:

```json
{
  "data": [
    {
      "id": 1,
      "member": {
        "id": 4,
        "nama": "Teddy Palastri"
      },
      "petugas": {
        "id": 2,
        "name": "Siti Rahmawati"
      },
      "tanggal_pinjam": "2026-11-18",
      "tanggal_kembali": "2026-11-25",
      "tanggal_dikembalikan": null,
      "status": "terlambat",
      "books": [
        {
          "id": 10,
          "judul": "Nusantaranger"
        },
        {
          "id": 12,
          "judul": "Genetika Modern"
        },
        {
          "id": 2,
          "judul": "Sejarah Indonesia Modern"
        }
      ]
    },
    "... (dipotong)"
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/loans?page=1",
    "last": "http://127.0.0.1:8000/api/loans?page=2",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/loans?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "http://127.0.0.1:8000/api/loans",
    "per_page": 10,
    "to": 10,
    "total": 11
  }
}
```

### PUT `/api/loans/11/kembalikan` — Kembalikan buku pada transaksi

Status: **200**

Response:

```json
{
  "data": {
    "id": 11,
    "member": {
      "id": 1,
      "nama": "Lukita Uyainah"
    },
    "petugas": {
      "id": 1,
      "name": "Bambang Sutrisno"
    },
    "tanggal_pinjam": "2026-11-26",
    "tanggal_kembali": "2026-12-03",
    "tanggal_dikembalikan": "2026-11-26",
    "status": "dikembalikan",
    "books": [
      {
        "id": 21,
        "judul": "Bumi Manusia (Edisi Revisi)"
      }
    ]
  }
}
```

### PUT `/api/loans/999/kembalikan` — Kembalikan transaksi yang tidak ada

Status: **404**

Response:

```json
{
  "message": "No query results for model [App\\Models\\Loan] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```

### GET `/api/stats` — Ringkasan statistik

Status: **200**

Response:

```json
{
  "total_buku": 21,
  "total_anggota": 15,
  "peminjaman_aktif": 2
}
```

### DELETE `/api/books/21` — Hapus buku yang sudah tercatat di peminjaman (ditolak)

Status: **409**

Response:

```json
{
  "message": "Buku tidak bisa dihapus karena tercatat di transaksi peminjaman."
}
```

### DELETE `/api/books/22` — Hapus buku

Status: **200**

Response:

```json
{
  "message": "Buku berhasil dihapus."
}
```

### DELETE `/api/books/999` — Hapus buku yang tidak ada

Status: **404**

Response:

```json
{
  "message": "No query results for model [App\\Models\\Book] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException"
}
```
