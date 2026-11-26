{{-- File: resources/views/members/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Anggota')

@section('content')
    <p><a href="{{ route('members.index') }}">&larr; Kembali ke daftar anggota</a></p>

    <h1>Detail Anggota</h1>

    <table>
        <tr>
            <th class="label">Nama</th>
            <td>{{ $member['nama'] }}</td>
        </tr>
        <tr>
            <th class="label">NIM</th>
            <td>{{ $member['nim'] }}</td>
        </tr>
        <tr>
            <th class="label">Email</th>
            <td>{{ $member['email'] }}</td>
        </tr>
        <tr>
            <th class="label">No. Telepon</th>
            <td>{{ $member['nomor_telepon'] }}</td>
        </tr>
        <tr>
            <th class="label">Alamat</th>
            <td>{{ $member['alamat'] }}</td>
        </tr>
        <tr>
            <th class="label">Status</th>
            <td>{{ ucfirst($member['status']) }}</td>
        </tr>
    </table>

    <p><a href="{{ route('members.edit', $member['id']) }}" class="btn">Edit Anggota</a></p>
@endsection
