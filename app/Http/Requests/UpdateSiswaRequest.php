<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Siswa;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $siswaRoute = $this->route('siswa');
        $ignoreId = $siswaRoute instanceof Siswa
            ? $siswaRoute->id
            : $siswaRoute;

        return [
            'nis' => [
                'required',
                'string',
                'size:16',
                Rule::unique('siswas', 'nis')->ignore($ignoreId),
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
            'nis.required'           => 'NIS wajib diisi',
            'nis.size'               => 'NIS harus 16 digit',
            'nis.unique'             => 'NIS sudah terdaftar',
            'nis.regex'              => 'NIS hanya boleh angka',
            'nama.required'          => 'Nama siswa wajib diisi',
            'nama.regex'             => 'Nama hanya boleh huruf',
            'nama_orangtua.required' => 'Nama orang tua wajib diisi',
            'nama_orangtua.regex'    => 'Nama hanya boleh huruf',
            'kelas.required'         => 'Kelas wajib dipilih',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
            'no_telepon.required'    => 'Nomor telepon wajib diisi',
            'no_telepon.regex'       => 'Format nomor telepon tidak valid',
        ];
    }

    // Intentionally no prepareForValidation here to avoid implicitly
    // changing `is_active` when the field is omitted on update.
}

