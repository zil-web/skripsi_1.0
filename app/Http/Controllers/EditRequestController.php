<?php
namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\AuditLog;
use App\Notifications\EditRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class EditRequestController extends Controller
{
    public function create($transaksiId): View|RedirectResponse
    {
        $transaksi = Transaksi::findOrFail($transaksiId);

        if ($transaksi->pendingEditRequest) {
            return back()->with('error', 'Sudah ada permintaan edit yang menunggu persetujuan kepsek.');
        }

        return view('edit-request.create', compact('transaksi'));
    }

    public function store(Request $request, $transaksiId): RedirectResponse
    {
        $validated = $request->validate([
            'new_jumlah' => 'required|integer|min:1',
            'new_jenis' => 'required|string',
            'new_keterangan' => 'nullable|string|max:500',
        ]);

        $transaksi = Transaksi::findOrFail($transaksiId);

        // Validasi: cek apakah ada permintaan edit yang masih pending
        if ($transaksi->pendingEditRequest) {
            return back()->with('error', 'Sudah ada permintaan edit yang menunggu persetujuan kepsek.');
        }

        // Validasi: cek minimal ada satu perubahan dari nilai lama
        $hasChanges = ($transaksi->jumlah != $validated['new_jumlah']) ||
                      ($transaksi->jenis != $validated['new_jenis']) ||
                      ($transaksi->keterangan != ($validated['new_keterangan'] ?? null));
        
        if (!$hasChanges) {
            return back()->with('warning', 
                'Perubahan yang Anda ajukan sama dengan data saat ini. Edit tidak diperlukan.');
        }

        // Validasi: jumlah baru tidak boleh negatif atau sangat ekstrim
        if ($validated['new_jumlah'] < 0) {
            return back()->with('error', 'Jumlah tidak boleh negatif.');
        }

        try {
            $editRequest = EditRequest::create([
                'transaksi_id' => $transaksi->id,
                'requested_by' => auth()->id(),
                'old_jumlah' => $transaksi->jumlah,
                'new_jumlah' => $validated['new_jumlah'],
                'old_jenis' => $transaksi->jenis,
                'new_jenis' => $validated['new_jenis'],
                'old_keterangan' => $transaksi->keterangan,
                'new_keterangan' => $validated['new_keterangan'],
                'status' => 'pending',
            ]);

            // Log audit
            \App\Models\AuditLog::create([
                'aktivitas' => 'Admin mengajukan edit transaksi ID ' . $transaksi->id . 
                              ' (dari ' . number_format($transaksi->jumlah, 0, ',', '.') . 
                              ' menjadi ' . number_format($validated['new_jumlah'], 0, ',', '.') . ')',
                'tanggal' => now(),
                'id_admin' => auth()->id(),
                'id_transaksi' => $transaksi->id,
            ]);

            $kepsekUsers = User::where('role', 'kepsek')->get();
            Notification::send($kepsekUsers, new EditRequestNotification($editRequest));

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Permintaan edit telah dikirim, menunggu persetujuan Kepala Sekolah.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
