{{-- File: resources/views/books/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
    <p><a href="{{ route('books.index') }}">&larr; Kembali ke daftar buku</a></p>

    <h1>Detail Buku</h1>

    <table>
        <tr>
            <th class="label">Judul</th>
            <td>{{ $book['judul'] }}</td>
        </tr>
        <tr>
            <th class="label">Penulis</th>
            <td>{{ $book['penulis'] }}</td>
        </tr>
        <tr>
            <th class="label">Penerbit</th>
            <td>{{ $book['penerbit'] }}</td>
        </tr>
        <tr>
            <th class="label">Tahun Terbit</th>
            <td>{{ $book['tahun_terbit'] }}</td>
        </tr>
        <tr>
            <th class="label">ISBN</th>
            <td>{{ $book['isbn'] ?? '-' }}</td>
        </tr>
        <tr>
            <th class="label">Stok</th>
            <td>{{ $book['stok'] }}</td>
        </tr>
        <tr>
            <th class="label">Kategori</th>
            <td>{{ $book['kategori'] }}</td>
        </tr>
    </table>

    <p><a href="{{ route('books.edit', $book['id']) }}" class="btn">Edit Buku</a></p>
@endsection
