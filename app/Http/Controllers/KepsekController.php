<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class KepsekController extends Controller
{
    private function currentReviewerId(): int|string|null
    {
        return auth()->guard('kepsek')->id() ?? auth()->id();
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

        $editRequest->transaksi->update([
            'jumlah' => $editRequest->new_jumlah,
            'jenis' => $editRequest->new_jenis,
            'keterangan' => $editRequest->new_keterangan,
        ]);

        $editRequest->update([
            'status' => 'approved',
            'reviewed_by' => $this->currentReviewerId(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Perubahan transaksi telah disetujui.');
    }

    public function rejectEdit(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'catatan_kepsek' => 'nullable|string|max:300',
        ]);

        $editRequest = EditRequest::findOrFail($id);

        $editRequest->update([
            'status' => 'rejected',
            'catatan_kepsek' => $request->catatan_kepsek,
            'reviewed_by' => $this->currentReviewerId(),
            'reviewed_at' => now(),
        ]);

        return back()->with('info', 'Permintaan edit ditolak.');
    }

    public function approvePengeluaran($id): RedirectResponse
    {
        $transaksi = Transaksi::where('tipe', 'pengeluaran')->findOrFail($id);

        $transaksi->update([
            'status' => 'approved',
            'reviewed_by' => $this->currentReviewerId(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pengeluaran telah disetujui.');
    }

    public function rejectPengeluaran(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'catatan_kepsek' => 'nullable|string|max:300',
        ]);

        $transaksi = Transaksi::where('tipe', 'pengeluaran')->findOrFail($id);

        $transaksi->update([
            'status' => 'rejected',
            'catatan_kepsek' => $request->catatan_kepsek,
            'reviewed_by' => $this->currentReviewerId(),
            'reviewed_at' => now(),
        ]);

        return back()->with('info', 'Pengeluaran ditolak.');
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
        $transaksi = Transaksi::with('siswa')->findOrFail($id);

        $raw = $transaksi->bukti_transaksi;
        $buktiUrl = $raw ? route('kepsek.transaksi.bukti', $id) : null;

        $existsRaw = $raw ? Storage::disk('public')->exists($raw) : false;

        return response()->json([
            'id' => $transaksi->id,
            'tanggal' => optional($transaksi->tanggal)->format('d/m/Y'),
            'keterangan' => $transaksi->keterangan,
            'jenis' => $transaksi->jenis,
            'jenis_transaksi' => $transaksi->jenis_transaksi ?? null,
            'jumlah' => $transaksi->jumlah,
            'status' => $transaksi->status,
            'siswa' => $transaksi->siswa ? [
                'id' => $transaksi->siswa->id,
                'nik' => $transaksi->siswa->nik,
                'nama' => $transaksi->siswa->nama,
                'kelas' => $transaksi->siswa->kelas
            ] : null,
            'bukti_raw' => $raw,
            'bukti_url' => $buktiUrl,
            'bukti_exists_raw' => $existsRaw,
        ]);
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
