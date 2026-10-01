<?php
// File: app/Http/Requests/StoreMemberRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'          => ['required', 'string', 'max:255'],
            'nim'           => ['required', 'string', 'max:20', 'unique:members,nim'],
            'email'         => ['required', 'email', 'max:255', 'unique:members,email'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string'],
            'status'        => ['required', 'in:aktif,nonaktif'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'          => 'Nama anggota wajib diisi.',
            'nama.max'               => 'Nama maksimal 255 karakter.',
            'nim.required'           => 'NIM wajib diisi.',
            'nim.unique'             => 'NIM sudah terdaftar.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'status.required'        => 'Status wajib dipilih.',
            'status.in'              => 'Status harus "aktif" atau "nonaktif".',
        ];
    }
}