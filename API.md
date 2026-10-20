# Dokumentasi REST API — App Perpustakaan

Base URL: `http://127.0.0.1:8000/api` · Semua request memakai header `Accept: application/json` (dan `Content-Type: application/json` untuk request dengan body).

Endpoint bersifat publik (belum memakai token), sesuai cakupan Pertemuan 9. Response `404` menampilkan field `exception` karena `APP_DEBUG=true` di lingkungan lokal; di produksi (`APP_DEBUG=false`) hanya `message` yang dikirim. Contoh response di bawah diambil langsung dari aplikasi yang sedang berjalan; daftar yang panjang dipotong ke satu item.

## Ringkasan Endpoint

| Method | URL | Body (jika ada) | Contoh Response (status + isi) |
|---|---|---|---|
| GET | `/api/books` | - | `200` `{"data": [{"id": 2, "judul": "Laskar Pelangi", "penulis": "Andrea Hirata", "penerbit": ...` |
| GET | `/api/books?judul=laskar` | - | `200` `{"data": [{"id": 2, "judul": "Laskar Pelangi", "penulis": "Andrea Hirata", "penerbit": ...` |
| GET | `/api/books/6` | - | `200` `{"data": {"id": 6, "judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "penerb...` |
| GET | `/api/books/999` | - | `404` `{"message": "No query results for model [App\\Models\\Book] 999", "exception": "Symfony...` |
| POST | `/api/books` | `{"judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "penerbit": "Hasta Mitra", "tahun_terbit": 1980, "isbn": "9789794330746", "stok": 4, "category_id": 1}` | `201` `{"data": {"id": 6, "judul": "Bumi Manusia", "penulis": "Pramoedya Ananta Toer", "penerb...` |
| POST | `/api/books` | `{}` (kosong) | `422` `{"message": "Judul buku wajib diisi. (and 5 more errors)", "errors": {"judul": ["Judul ...` |
| PUT | `/api/books/6` | `{"judul": "Bumi Manusia (Edisi Revisi)", "penulis": "Pramoedya Ananta Toer", "penerbit": "Hasta Mitra", "tahun_terbit": 1980, "isbn": "9789794330746", "stok": 5, "category_id": 1}` | `200` `{"data": {"id": 6, "judul": "Bumi Manusia (Edisi Revisi)", "penulis": "Pramoedya Ananta...` |
| GET | `/api/members` | - | `200` `{"data": [{"id": 1, "nama": "Rina W", "nim": "231", "email": "rina@k.ac.id", "nomor_tel...` |
| GET | `/api/members/1` | - | `200` `{"data": {"id": 1, "nama": "Rina W", "nim": "231", "email": "rina@k.ac.id", "nomor_tele...` |
| GET | `/api/members/999` | - | `404` `{"message": "No query results for model [App\\Models\\Member] 999", "exception": "Symfo...` |
| POST | `/api/loans` | `{"member_id": 1, "user_id": 1, "tanggal_pinjam": "2026-11-26", "tanggal_kembali": "2026-12-03", "book_ids": [6]}` | `201` `{"data": {"id": 5, "member": {"id": 1, "nama": "Rina W"}, "petugas": {"id": 1, "name": ...` |
| POST | `/api/loans` | `{"member_id": 1}` | `422` `{"message": "Petugas (user_id) wajib diisi. (and 3 more errors)", "errors": {"user_id":...` |
| GET | `/api/loans` | - | `200` `{"data": [{"id": 2, "member": {"id": 1, "nama": "Rina W"}, "petugas": {"id": 3, "name":...` |
| PUT | `/api/loans/5/kembalikan` | - | `200` `{"data": {"id": 5, "member": {"id": 1, "nama": "Rina W"}, "petugas": {"id": 1, "name": ...` |
| PUT | `/api/loans/999/kembalikan` | - | `404` `{"message": "No query results for model [App\\Models\\Loan] 999", "exception": "Symfony...` |
| GET | `/api/stats` | - | `200` `{"total_buku": 3, "total_anggota": 2, "peminjaman_aktif": 1}` |
| DELETE | `/api/books/6` | - | `409` `{"message": "Buku tidak bisa dihapus karena tercatat di transaksi peminjaman."}` |
| DELETE | `/api/books/7` | - | `200` `{"message": "Buku berhasil dihapus."}` |
| DELETE | `/api/books/999` | - | `404` `{"message": "No query results for model [App\\Models\\Book] 999", "exception": "Symfony...` |

## Detail Request & Response

### GET `/api/books` — Daftar buku (pagination 10/halaman). Query opsional: `?judul=`, `?category_id=`, `?page=`

Status: **200**

Response:

```json
{
  "data": [
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang",
      "tahun_terbit": "2005",
      "isbn": null,
      "stok": 3,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Fiksi-Edit"
      },
      "created_at": "2026-11-26T09:41:07.000000Z",
      "updated_at": "2026-11-26T09:41:07.000000Z"
    },
    "... (dipotong)"
  ],
  "links": {
    "first": "http://127.0.0.1:8123/api/books?page=1",
    "last": "http://127.0.0.1:8123/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8123/api/books",
    "per_page": 10,
    "to": 3,
    "total": 3
  }
}
```

### GET `/api/books?judul=laskar` — Filter judul (LIKE)

Status: **200**

Response:

```json
{
  "data": [
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang",
      "tahun_terbit": "2005",
      "isbn": null,
      "stok": 3,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Fiksi-Edit"
      },
      "created_at": "2026-11-26T09:41:07.000000Z",
      "updated_at": "2026-11-26T09:41:07.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8123/api/books?page=1",
    "last": "http://127.0.0.1:8123/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8123/api/books",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

### GET `/api/books/6` — Detail satu buku

Status: **200**

Response:

```json
{
  "data": {
    "id": 6,
    "judul": "Bumi Manusia",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": "1980",
    "isbn": "9789794330746",
    "stok": 4,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Fiksi-Edit"
    },
    "created_at": "2026-11-26T09:45:01.000000Z",
    "updated_at": "2026-11-26T09:45:01.000000Z"
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
    "id": 6,
    "judul": "Bumi Manusia",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": 1980,
    "isbn": "9789794330746",
    "stok": 4,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Fiksi-Edit"
    },
    "created_at": "2026-11-26T09:45:01.000000Z",
    "updated_at": "2026-11-26T09:45:01.000000Z"
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

### PUT `/api/books/6` — Ubah buku

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
    "id": 6,
    "judul": "Bumi Manusia (Edisi Revisi)",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": 1980,
    "isbn": "9789794330746",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Fiksi-Edit"
    },
    "created_at": "2026-11-26T09:45:01.000000Z",
    "updated_at": "2026-11-26T09:45:01.000000Z"
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
      "nama": "Rina W",
      "nim": "231",
      "email": "rina@k.ac.id",
      "nomor_telepon": "0812",
      "alamat": "Jl",
      "status": "nonaktif",
      "created_at": "2026-11-26T09:39:23.000000Z",
      "updated_at": "2026-11-26T09:39:23.000000Z"
    },
    "... (dipotong)"
  ],
  "links": {
    "first": "http://127.0.0.1:8123/api/members?page=1",
    "last": "http://127.0.0.1:8123/api/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8123/api/members",
    "per_page": 10,
    "to": 2,
    "total": 2
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
    "nama": "Rina W",
    "nim": "231",
    "email": "rina@k.ac.id",
    "nomor_telepon": "0812",
    "alamat": "Jl",
    "status": "nonaktif",
    "created_at": "2026-11-26T09:39:23.000000Z",
    "updated_at": "2026-11-26T09:39:23.000000Z"
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
    6
  ]
}
```

Response:

```json
{
  "data": {
    "id": 5,
    "member": {
      "id": 1,
      "nama": "Rina W"
    },
    "petugas": {
      "id": 1,
      "name": "Petugas Uji"
    },
    "tanggal_pinjam": "2026-11-26",
    "tanggal_kembali": "2026-12-03",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      {
        "id": 6,
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
      "id": 2,
      "member": {
        "id": 1,
        "nama": "Rina W"
      },
      "petugas": {
        "id": 3,
        "name": "Petugas Satu"
      },
      "tanggal_pinjam": "2026-11-26",
      "tanggal_kembali": "2026-12-03",
      "tanggal_dikembalikan": null,
      "status": "dipinjam",
      "books": [
        {
          "id": 2,
          "judul": "Laskar Pelangi"
        }
      ]
    },
    "... (dipotong)"
  ],
  "links": {
    "first": "http://127.0.0.1:8123/api/loans?page=1",
    "last": "http://127.0.0.1:8123/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8123/api/loans",
    "per_page": 10,
    "to": 3,
    "total": 3
  }
}
```

### PUT `/api/loans/5/kembalikan` — Kembalikan buku pada transaksi

Status: **200**

Response:

```json
{
  "data": {
    "id": 5,
    "member": {
      "id": 1,
      "nama": "Rina W"
    },
    "petugas": {
      "id": 1,
      "name": "Petugas Uji"
    },
    "tanggal_pinjam": "2026-11-26",
    "tanggal_kembali": "2026-12-03",
    "tanggal_dikembalikan": "2026-11-26",
    "status": "dikembalikan",
    "books": [
      {
        "id": 6,
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
  "total_buku": 3,
  "total_anggota": 2,
  "peminjaman_aktif": 1
}
```

### DELETE `/api/books/6` — Hapus buku yang sudah tercatat di peminjaman (ditolak)

Status: **409**

Response:

```json
{
  "message": "Buku tidak bisa dihapus karena tercatat di transaksi peminjaman."
}
```

### DELETE `/api/books/7` — Hapus buku

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
