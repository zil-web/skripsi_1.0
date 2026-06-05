<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\User;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class KepsekController extends Controller
{
    private function currentReviewerId(): int|string|null
    {
        $userId = Auth::id();
        if ($userId) {
            return $userId;
        }

        $kepsek = Auth::guard('kepsek')->user();
        if (!$kepsek) {
            return null;
        }

        $linkedUserId = User::query()
            ->where('username', $kepsek->username)
            ->value('id');

        \Log::debug('Kepsek reviewer lookup', [
            'kepsek_id' => $kepsek->id,
            'kepsek_username' => $kepsek->username,
            'auth_id' => $userId,
            'linked_user_id' => $linkedUserId,
        ]);

        return $linkedUserId ? (int) $linkedUserId : null;
    }

    public function index(): View
    {
        $editRequests = EditRequest::with(['transaksi', 'requestedBy'])->pending()->latest()->get();

        $pengeluaran = Transaksi::where('tipe', 'pengeluaran')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('kepsek.approve-list', compact('editRequests', 'pengeluaran'));
    }

    public function approveEdit($id): RedirectResponse
    {
        $editRequest = EditRequest::with('transaksi')->findOrFail($id);

        // Validasi: edit request harus dalam status pending
        if ($editRequest->status !== 'pending') {
            return back()->with('error', 
                'Permintaan edit tidak dapat disetujui. Status saat ini: ' . $editRequest->status);
        }

        // Validasi: transaksi masih harus ada dan tidak dihapus
        if (!$editRequest->transaksi) {
            return back()->with('error', 'Transaksi tidak ditemukan atau telah dihapus.');
        }

        // Validasi: cek apakah nilai baru minimal berbeda dari nilai lama
        $hasChanges = ($editRequest->old_jumlah != $editRequest->new_jumlah) ||
                      ($editRequest->old_jenis != $editRequest->new_jenis) ||
                      ($editRequest->old_keterangan != $editRequest->new_keterangan);
        
        if (!$hasChanges) {
            return back()->with('warning', 
                'Tidak ada perubahan data yang signifikan untuk disetujui.');
        }

        try {
            $reviewedBy = $this->currentReviewerId();
            if (!$reviewedBy) {
                return back()->with('error', 'Akun Kepala Sekolah belum terhubung dengan user yang valid.');
            }

            $editRequest->transaksi->update([
                'jumlah' => $editRequest->new_jumlah,
                'jenis' => $editRequest->new_jenis,
                'keterangan' => $editRequest->new_keterangan,
            ]);

            $editRequest->update([
                'status' => 'approved',
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            // Log audit
            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menyetujui edit transaksi ID ' . $editRequest->transaksi_id . 
                              ' (dari ' . $editRequest->old_jumlah . ' menjadi ' . $editRequest->new_jumlah . ')',
                'tanggal' => now(),
                'id_admin' => $reviewedBy,
                'id_transaksi' => $editRequest->transaksi_id,
            ]);

            return back()->with('success', 'Perubahan transaksi telah disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyetujui: ' . $e->getMessage());
        }
    }

    public function rejectEdit(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_kepsek' => 'required|string|min:10|max:300',
        ], [
            'catatan_kepsek.required' => 'Catatan penolakan wajib diisi',
            'catatan_kepsek.min' => 'Catatan minimal 10 karakter',
            'catatan_kepsek.max' => 'Catatan maksimal 300 karakter',
        ]);

        $editRequest = EditRequest::findOrFail($id);

        // Validasi: edit request harus dalam status pending
        if ($editRequest->status !== 'pending') {
            return back()->with('error', 
                'Permintaan edit tidak dapat ditolak. Status saat ini: ' . $editRequest->status);
        }

        try {
            $editRequest->update([
                'status' => 'rejected',
                'catatan_kepsek' => $validated['catatan_kepsek'],
                'reviewed_by' => $this->currentReviewerId(),
                'reviewed_at' => now(),
            ]);

            // Log audit
            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menolak edit transaksi ID ' . $editRequest->transaksi_id . 
                              ' - Alasan: ' . $validated['catatan_kepsek'],
                'tanggal' => now(),
                'id_admin' => $this->currentReviewerId(),
                'id_transaksi' => $editRequest->transaksi_id,
            ]);

            return back()->with('info', 'Permintaan edit ditolak dengan catatan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menolak: ' . $e->getMessage());
        }
    }

    public function approvePengeluaran($id): RedirectResponse
    {
        $transaksi = Transaksi::where('tipe', 'pengeluaran')->findOrFail($id);

        // Validasi: transaksi harus dalam status pending
        if ($transaksi->status->value !== 'pending') {
            return back()->with('error', 
                'Pengeluaran tidak dapat disetujui. Status saat ini: ' . $transaksi->status->value);
        }

        // Validasi: cek apakah sudah di-approve sebelumnya oleh reviewer yang sama
        $reviewedBy = $this->currentReviewerId();
        if (!$reviewedBy) {
            return back()->with('error', 'Akun Kepala Sekolah belum terhubung dengan user yang valid.');
        }
        if ($transaksi->reviewed_by && $transaksi->reviewed_by == $reviewedBy) {
            return back()->with('error', 
                'Anda sudah melakukan approval untuk pengeluaran ini sebelumnya.');
        }

        try {
            $transaksi->update([
                'status' => 'approved',
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            // Log audit
            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menyetujui pengeluaran ID ' . $transaksi->id . 
                              ' - Rp ' . number_format($transaksi->jumlah, 0, ',', '.'),
                'tanggal' => now(),
                'id_admin' => $reviewedBy,
                'id_transaksi' => $transaksi->id,
            ]);

            return back()->with('success', 'Pengeluaran telah disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPengeluaran(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_kepsek' => 'required|string|min:10|max:300',
        ], [
            'catatan_kepsek.required' => 'Catatan penolakan wajib diisi',
            'catatan_kepsek.min' => 'Catatan minimal 10 karakter',
            'catatan_kepsek.max' => 'Catatan maksimal 300 karakter',
        ]);

        $transaksi = Transaksi::where('tipe', 'pengeluaran')->findOrFail($id);

        // Validasi: transaksi harus dalam status pending
        if ($transaksi->status->value !== 'pending') {
            return back()->with('error', 
                'Pengeluaran tidak dapat ditolak. Status saat ini: ' . $transaksi->status->value);
        }

        // Validasi: cek apakah sudah di-process sebelumnya oleh reviewer yang sama
        $reviewedBy = $this->currentReviewerId();
        if (!$reviewedBy) {
            return back()->with('error', 'Akun Kepala Sekolah belum terhubung dengan user yang valid.');
        }
        if ($transaksi->reviewed_by && $transaksi->reviewed_by == $reviewedBy) {
            return back()->with('error', 
                'Anda sudah melakukan approval untuk pengeluaran ini sebelumnya.');
        }

        try {
            $transaksi->update([
                'status' => 'rejected',
                'catatan_kepsek' => $validated['catatan_kepsek'],
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            // Log audit
            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menolak pengeluaran ID ' . $transaksi->id . 
                              ' - Rp ' . number_format($transaksi->jumlah, 0, ',', '.') . 
                              ' - Alasan: ' . $validated['catatan_kepsek'],
                'tanggal' => now(),
                'id_admin' => $reviewedBy,
                'id_transaksi' => $transaksi->id,
            ]);

            return back()->with('info', 'Pengeluaran ditolak dengan catatan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function transaksi(Request $request)
    {
        $query = Transaksi::with(['pendingEditRequest', 'siswa', 'reviewedBy'])->latest();

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $from = Carbon::parse($request->input('tanggal_dari'))->startOfDay()->toDateString();
            $to = Carbon::parse($request->input('tanggal_sampai'))->endOfDay()->toDateString();
            $query->whereBetween('tanggal', [$from, $to]);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('nominal')) {
            $nominal = (int) str_replace(['.', ','], '', (string) $request->input('nominal'));
            $query->where('jumlah', $nominal);
        }

        if ($request->filled('nominal_min') || $request->filled('nominal_max')) {
            $nominalMin = $request->filled('nominal_min')
                ? (int) str_replace(['.', ','], '', (string) $request->input('nominal_min'))
                : null;
            $nominalMax = $request->filled('nominal_max')
                ? (int) str_replace(['.', ','], '', (string) $request->input('nominal_max'))
                : null;

            if ($nominalMin !== null && $nominalMax !== null) {
                $query->whereBetween('jumlah', [$nominalMin, $nominalMax]);
            } elseif ($nominalMin !== null) {
                $query->where('jumlah', '>=', $nominalMin);
            } elseif ($nominalMax !== null) {
                $query->where('jumlah', '<=', $nominalMax);
            }
        }

        $transaksis = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('kepsek.transaksi', compact('transaksis'));
    }

    public function exportCsv(Request $request)
    {
        $query = Transaksi::with(['user', 'siswa', 'reviewedBy'])->latest();

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $from = Carbon::parse($request->input('tanggal_dari'))->startOfDay()->toDateString();
            $to = Carbon::parse($request->input('tanggal_sampai'))->endOfDay()->toDateString();
            $query->whereBetween('tanggal', [$from, $to]);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('nominal')) {
            $nominal = (int) str_replace(['.', ','], '', (string) $request->input('nominal'));
            $query->where('jumlah', $nominal);
        }

        if ($request->filled('nominal_min') || $request->filled('nominal_max')) {
            $nominalMin = $request->filled('nominal_min')
                ? (int) str_replace(['.', ','], '', (string) $request->input('nominal_min'))
                : null;
            $nominalMax = $request->filled('nominal_max')
                ? (int) str_replace(['.', ','], '', (string) $request->input('nominal_max'))
                : null;

            if ($nominalMin !== null && $nominalMax !== null) {
                $query->whereBetween('jumlah', [$nominalMin, $nominalMax]);
            } elseif ($nominalMin !== null) {
                $query->where('jumlah', '>=', $nominalMin);
            } elseif ($nominalMax !== null) {
                $query->where('jumlah', '<=', $nominalMax);
            }
        }

        $transaksis = $query->orderByDesc('tanggal')->get();

        $filename = 'laporan-transaksi';
        if ($request->filled('tanggal_dari')) {
            $filename .= '-dari-' . $request->input('tanggal_dari');
        }
        if ($request->filled('tanggal_sampai')) {
            $filename .= '-sampai-' . $request->input('tanggal_sampai');
        }
        if ($request->filled('jenis')) {
            $filename .= '-' . $request->input('jenis');
        }
        $filename .= '-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($transaksis) {
            $file = fopen('php://output', 'w');
            $scalar = static function ($value): string {
                if ($value instanceof \BackedEnum) {
                    return (string) $value->value;
                }

                return (string) $value;
            };

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'Tanggal',
                'Tipe',
                'Jumlah (Rp)',
                'Jenis',
                'Keterangan',
                'Status',
                'Nama Siswa',
                'NIK Siswa',
                'Dicatat Oleh',
                'Disetujui Oleh',
                'Tanggal Disetujui',
                'Catatan Penolakan',
            ]);

            foreach ($transaksis as $i => $t) {
                $statusValue = $scalar($t->status);
                $tipeValue = $scalar($t->tipe);
                $jenisValue = $scalar($t->jenis);

                fputcsv($file, [
                    $i + 1,
                    Carbon::parse($t->tanggal)->format('d/m/Y'),
                    ucfirst($tipeValue !== '' ? $tipeValue : '-'),
                    $t->jumlah ?? 0,
                    $jenisValue !== '' ? $jenisValue : '-',
                    $t->keterangan ?? '-',
                    ucfirst($statusValue !== '' ? $statusValue : '-'),
                    $t->siswa->nama ?? '-',
                    $t->siswa->nik ?? '-',
                    $t->user->name ?? '-',
                    optional($t->reviewedBy)->name ?? '-',
                    $t->reviewed_at ? Carbon::parse($t->reviewed_at)->format('d/m/Y H:i') : '-',
                    $t->catatan_kepsek ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function transaksiDetail($id)
    {
        $transaksi = Transaksi::with(['siswa', 'user', 'reviewedBy'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id'              => $transaksi->id,
                'tanggal'         => optional($transaksi->tanggal)->format('d/m/Y'),
                'jenis'           => $transaksi->jenis instanceof \BackedEnum
                    ? $transaksi->jenis->value
                    : $transaksi->jenis,
                'jenis_transaksi' => ucfirst($transaksi->jenis instanceof \BackedEnum
                    ? $transaksi->jenis->value
                    : ($transaksi->jenis ?? '-')),
                'jumlah'          => $transaksi->jumlah,
                'keterangan'      => $transaksi->keterangan,
                'status'          => $transaksi->status instanceof \BackedEnum
                    ? $transaksi->status->value
                    : $transaksi->status,
                'siswa'           => $transaksi->siswa ? [
                    'nama'  => $transaksi->siswa->nama,
                    'nik'   => $transaksi->siswa->nik,
                    'kelas' => $transaksi->siswa->kelas,
                ] : null,
                'bukti_url'       => $transaksi->bukti_transaksi
                    ? asset('storage/' . $transaksi->bukti_transaksi)
                    : null,
                'bukti_raw'       => $transaksi->bukti_transaksi,
            ]);
        }

        // Non-AJAX fallback (optional — redirect to list)
        return redirect()->route('kepsek.transaksi');
    }

    public function bukti($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        if (!$transaksi->bukti_transaksi) {
            abort(404, 'Bukti tidak tersedia');
        }

        $path = $transaksi->bukti_transaksi;
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti tidak ditemukan');
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        return response($file, 200, [
            'Content-Type' => $mimeType ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
