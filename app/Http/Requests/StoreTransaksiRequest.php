<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_transaksi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'id_siswa' => ['nullable', 'exists:siswas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal transaksi harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',

            'jumlah.required' => 'Jumlah transaksi harus diisi.',
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah minimal 1.',

            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan maksimal :max karakter.',

            'bukti_transaksi.required' => 'Bukti transaksi harus diisi.',
            'bukti_transaksi.file' => 'Bukti transaksi harus berupa file.',
            'bukti_transaksi.mimes' => 'Bukti transaksi harus berformat JPG, JPEG, PNG, atau PDF.',
            'bukti_transaksi.max' => 'Ukuran bukti transaksi maksimal :max kilobyte.',

            'id_siswa.exists' => 'Siswa yang dipilih tidak ditemukan.',
        ];
    }
}
