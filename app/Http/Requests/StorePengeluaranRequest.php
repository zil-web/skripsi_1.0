<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:500',
            'jenis_pengeluaran' => 'required|in:ATK,Konsumsi Harian,Pembelian Aset,Renovasi,Kegiatan Besar,Lain-lain',
            'bukti_transaksi' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'id_siswa' => 'nullable|exists:siswas,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal harus diisi.',
            'jumlah.required' => 'Jumlah harus diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'jenis_pengeluaran.required' => 'Jenis pengeluaran harus dipilih.',
            'bukti_transaksi.required' => 'Bukti transaksi harus diisi.',
        ];
    }
}
