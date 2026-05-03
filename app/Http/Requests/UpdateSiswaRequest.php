<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nik' => [
                'required',
                'string',
                'size:16',
                'unique:siswas,nik,'.$this->route('siswa'),
                'regex:/^[0-9]+$/',
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\.,]+$/',
            ],
            'nama_orangtua' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\.,]+$/',
            ],
            'kelas'         => 'required|string|max:10',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telepon'    => [
                'nullable',
                'string',
                'max:15',
                'regex:/^[0-9\+\-\s]+$/',
            ],
            'alamat'        => 'nullable|string|max:255',
            'is_active'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required'           => 'NIK wajib diisi',
            'nik.size'               => 'NIK harus 16 digit',
            'nik.unique'             => 'NIK sudah terdaftar',
            'nik.regex'              => 'NIK hanya boleh angka',
            'nama.required'          => 'Nama siswa wajib diisi',
            'nama.regex'             => 'Nama hanya boleh huruf',
            'nama_orangtua.required' => 'Nama orang tua wajib diisi',
            'nama_orangtua.regex'    => 'Nama hanya boleh huruf',
            'kelas.required'         => 'Kelas wajib dipilih',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
            'no_telepon.regex'       => 'Format nomor telepon tidak valid',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->is_active ?? true,
        ]);
    }
}
