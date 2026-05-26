<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
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
                'unique:siswas,nik',
                'regex:/^[0-9]+$/',
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\s\.,]+$/u',
            ],
            'nama_orangtua' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\s\.,]+$/u',
            ],
            'kelas'         => 'required|string|max:10',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telepon'    => [
                'required',
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
            'nama.regex'             => 'Nama hanya boleh huruf dan spasi',
            'nama_orangtua.required' => 'Nama orang tua wajib diisi',
            'nama_orangtua.regex'    => 'Nama hanya boleh huruf dan spasi',
            'kelas.required'         => 'Kelas wajib dipilih',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
            'no_telepon.required'    => 'Nomor telepon wajib diisi',
            'no_telepon.regex'       => 'Format nomor telepon tidak valid',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalisasi input agar validasi boolean dan format lebih andal
        $nik = $this->nik ?? null;
        if ($nik !== null) {
            // hapus semua non-digit
            $nik = preg_replace('/\D+/', '', $nik);
        }

        $no = $this->no_telepon ?? null;
        if ($no !== null) {
            // hanya izinkan digit, plus, spasi dan tanda minus
            $no = preg_replace('/[^0-9\+\-\s]/u', '', $no);
        }

        $isActive = $this->is_active ?? true;
        // terima 'on', '1', true, 'true' sebagai true
        if (is_string($isActive)) {
            $lower = strtolower($isActive);
            $isActive = in_array($lower, ['1', 'true', 'on'], true) ? true : false;
        } else {
            $isActive = (bool) $isActive;
        }

        $this->merge([
            'nik' => $nik,
            'no_telepon' => $no,
            'is_active' => $isActive,
            'nama' => $this->nama ? trim(preg_replace('/\s+/', ' ', $this->nama)) : null,
            'nama_orangtua' => $this->nama_orangtua ? trim(preg_replace('/\s+/', ' ', $this->nama_orangtua)) : null,
        ]);
    }
}
