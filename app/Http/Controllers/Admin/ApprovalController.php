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
            $status = 'approved';

            $transaksi->update(['status' => $status]);

            if (Auth::guard('web')->check()) {
                AuditLog::create([
                    'aktivitas' => 'Menyetujui transaksi ID ' . $transaksi->id,
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
            $status = 'rejected';

            $transaksi->update(['status' => $status]);

            if (Auth::guard('web')->check()) {
                AuditLog::create([
                    'aktivitas' => 'Menolak transaksi ID ' . $transaksi->id,
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
            }

            return redirect()->back()->with('success', 'Transaksi berhasil ditolak');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }
    }
}
