<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaksi;
use App\Models\AuditLog;
use App\Models\Validasi;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Approve a pending transaksi
     */
    public function approve($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            
            // Validasi: transaksi harus dalam status pending
            if ($transaksi->status->value !== 'pending') {
                return redirect()->back()->with('error', 
                    'Transaksi tidak dapat disetujui. Status saat ini: ' . $transaksi->status->value);
            }
            
            // Validasi: cek apakah sudah ada validasi sebelumnya
            $existingValidasi = Validasi::where('id_transaksi', $transaksi->id)
                ->where('id_kepsek', Auth::guard('kepsek')->id() ?? Auth::id())
                ->first();
            
            if ($existingValidasi && $existingValidasi->status !== 'pending') {
                return redirect()->back()->with('error', 
                    'Anda sudah melakukan approval untuk transaksi ini sebelumnya.');
            }
            
            $status = 'approved';
            $currentUserId = Auth::guard('kepsek')->id() ?? Auth::guard('web')->id();

            $transaksi->update([
                'status' => $status,
                'reviewed_by' => $currentUserId,
                'reviewed_at' => now(),
            ]);

            if (Auth::guard('web')->check()) {
                AuditLog::create([
                    'aktivitas' => 'Admin menyetujui transaksi ID ' . $transaksi->id . ' (Admin)',
                    'tanggal' => now(),
                    'id_admin' => Auth::guard('web')->id(),
                    'id_transaksi' => $transaksi->id,
                ]);
            }

            if (Auth::guard('kepsek')->check()) {
                Validasi::updateOrCreate(
                    [
                        'id_transaksi' => $transaksi->id,
                        'id_kepsek' => Auth::guard('kepsek')->id(),
                    ],
                    [
                        'status' => $status,
                        'catatan' => null,
                    ]
                );
                
                AuditLog::create([
                    'aktivitas' => 'Kepala Sekolah menyetujui transaksi ID ' . $transaksi->id . ' (Kepsek)',
                    'tanggal' => now(),
                    'id_admin' => Auth::guard('kepsek')->id(),
                    'id_transaksi' => $transaksi->id,
                ]);
            }

            return redirect()->back()->with('success', 'Transaksi berhasil disetujui');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }
    }

    /**
     * Reject a pending transaksi
     */
    public function reject($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            
            // Validasi: transaksi harus dalam status pending
            if ($transaksi->status->value !== 'pending') {
                return redirect()->back()->with('error', 
                    'Transaksi tidak dapat ditolak. Status saat ini: ' . $transaksi->status->value);
            }
            
            // Validasi: cek apakah sudah ada validasi sebelumnya
            $existingValidasi = Validasi::where('id_transaksi', $transaksi->id)
                ->where('id_kepsek', Auth::guard('kepsek')->id() ?? Auth::id())
                ->first();
            
            if ($existingValidasi && $existingValidasi->status !== 'pending') {
                return redirect()->back()->with('error', 
                    'Anda sudah melakukan approval untuk transaksi ini sebelumnya.');
            }
            
            $status = 'rejected';
            $currentUserId = Auth::guard('kepsek')->id() ?? Auth::guard('web')->id();
            $catatan = request()->input('catatan') ?? 'Tidak ada alasan yang diberikan';

            $transaksi->update([
                'status' => $status,
                'catatan_kepsek' => $catatan,
                'reviewed_by' => $currentUserId,
                'reviewed_at' => now(),
            ]);

            if (Auth::guard('web')->check()) {
                AuditLog::create([
                    'aktivitas' => 'Admin menolak transaksi ID ' . $transaksi->id . ' - Alasan: ' . $catatan,
                    'tanggal' => now(),
                    'id_admin' => Auth::guard('web')->id(),
                    'id_transaksi' => $transaksi->id,
                ]);
            }

            if (Auth::guard('kepsek')->check()) {
                Validasi::updateOrCreate(
                    [
                        'id_transaksi' => $transaksi->id,
                        'id_kepsek' => Auth::guard('kepsek')->id(),
                    ],
                    [
                        'status' => $status,
                        'catatan' => $catatan,
                    ]
                );
                
                AuditLog::create([
                    'aktivitas' => 'Kepala Sekolah menolak transaksi ID ' . $transaksi->id . ' - Alasan: ' . $catatan,
                    'tanggal' => now(),
                    'id_admin' => Auth::guard('kepsek')->id(),
                    'id_transaksi' => $transaksi->id,
                ]);
            }

            return redirect()->back()->with('info', 'Transaksi berhasil ditolak dengan catatan');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }
    }
}
