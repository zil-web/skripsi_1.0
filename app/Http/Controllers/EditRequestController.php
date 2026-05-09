<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Transaksi;
use App\Models\User;
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
        $request->validate([
            'new_jumlah' => 'required|integer|min:1',
            'new_jenis' => 'required|string',
            'new_keterangan' => 'nullable|string|max:500',
        ]);

        $transaksi = Transaksi::findOrFail($transaksiId);

        if ($transaksi->pendingEditRequest) {
            return back()->with('error', 'Sudah ada permintaan edit yang menunggu persetujuan kepsek.');
        }

        $editRequest = EditRequest::create([
            'transaksi_id' => $transaksi->id,
            'requested_by' => auth()->id(),
            'old_jumlah' => $transaksi->jumlah,
            'new_jumlah' => $request->new_jumlah,
            'old_jenis' => $transaksi->jenis,
            'new_jenis' => $request->new_jenis,
            'old_keterangan' => $transaksi->keterangan,
            'new_keterangan' => $request->new_keterangan,
            'status' => 'pending',
        ]);

        $kepsekUsers = User::where('role', 'kepsek')->get();
        Notification::send($kepsekUsers, new EditRequestNotification($editRequest));

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Permintaan edit telah dikirim, menunggu persetujuan Kepala Sekolah.');
    }
}
