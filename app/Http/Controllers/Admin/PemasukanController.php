<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\Pemasukan;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PemasukanController extends Controller
{
    /**
     * Display a listing of pemasukan (income transactions).
     *
     * Shows filtered list of transaksis with jenis='pemasukan' with pagination.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pemasukan::query()
            ->where('id_admin', $userId)
            ->with('siswa')
            ->orderByDesc('tanggal');

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Search by keterangan
        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        // Paginate results
        $pemasukkans = $query->paginate(10)->withQueryString();

        // Calculate total pemasukan (all, no status filtering needed)
        $totalPemasukan = $query->clone()->sum('jumlah');

        // Get siswas for modal form
        $siswas = Siswa::select('id', 'nama', 'kelas')->get();

        return view('admin.pemasukan.index', compact('pemasukkans', 'totalPemasukan', 'siswas'));
    }

    /**
     * Store a newly created pemasukan transaction in storage (via modal).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis_pemasukan' => ['required', 'in:SPP,Donasi,Dana BOS,Lain-lain'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_transaksi' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'jenis_pemasukan.required' => 'Jenis pemasukan harus dipilih.',
            'jenis_pemasukan.in' => 'Jenis pemasukan tidak valid.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'bukti_transaksi.required' => 'Bukti transaksi harus diisi.',
            'bukti_transaksi.file' => 'Bukti transaksi harus berupa file.',
            'bukti_transaksi.mimes' => 'Format bukti transaksi harus JPG, JPEG, PNG, atau PDF.',
            'bukti_transaksi.max' => 'Ukuran bukti transaksi maksimal 2MB.',
        ]);

        // Determine if this is a bulk SPP submission
        $isBulkSPP = $request->input('jenis_pemasukan') === 'SPP' && $request->has('siswa_list');

        // Handle file upload if provided (for bulk we store once)
        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti', 'public');
        }

        if ($isBulkSPP) {
            // siswa_list may be JSON string if sent via FormData
            $raw = $request->input('siswa_list');
            $siswaList = [];
            if (is_string($raw)) {
                $siswaList = json_decode($raw, true) ?: [];
            } elseif (is_array($raw)) {
                $siswaList = $raw;
            }

            if (!is_array($siswaList) || count($siswaList) === 0) {
                if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                return response()->json(['success' => false, 'message' => 'Tidak ada siswa yang dipilih.'], 422);
            }

            // Validate each item
            foreach ($siswaList as $item) {
                if (!isset($item['siswa_id']) || !isset($item['jumlah'])) {
                    if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                    return response()->json(['success' => false, 'message' => 'Format data siswa tidak valid.'], 422);
                }
                if (!is_numeric($item['jumlah']) || intval($item['jumlah']) <= 0) {
                    if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                    return response()->json(['success' => false, 'message' => 'Jumlah harus berupa angka > 0.'], 422);
                }
                if (!Siswa::where('id', $item['siswa_id'])->exists()) {
                    if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                    return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan: ' . $item['siswa_id']], 422);
                }
            }

            DB::beginTransaction();
            try {
                $count = 0;
                foreach ($siswaList as $item) {
                    $transaksi = Pemasukan::create([
                        'tanggal' => $request->input('tanggal'),
                        'jumlah' => intval($item['jumlah']),
                        'keterangan' => $request->input('keterangan') ?? '',
                        'jenis_transaksi' => 'SPP',
                        'siswa_id' => $item['siswa_id'],
                        'bukti_transaksi' => $buktiPath,
                        'id_admin' => $user->id,
                    ]);

                    AuditLog::create([
                        'aktivitas' => "Admin {$user->name} menambahkan transaksi SPP untuk siswa ID {$item['siswa_id']} sebesar " . rupiah($transaksi->jumlah),
                        'tanggal' => now(),
                        'id_admin' => $user->id,
                        'id_transaksi' => $transaksi->id,
                    ]);

                    $count++;
                }

                DB::commit();
                return response()->json(['success' => true, 'count' => $count]);
            } catch (\Throwable $e) {
                DB::rollBack();
                report($e);
                if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan pemasukan.'], 500);
            }
        }

        // Fallback: original single-entry handling
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'jenis_pemasukan' => ['required', 'in:SPP,Donasi,Dana BOS,Lain-lain'],
            'siswa_id' => ['required_if:jenis_pemasukan,SPP', 'nullable', 'exists:siswas,id'],
        ]);

        // Wrap in DB transaction to ensure atomicity
        DB::beginTransaction();
        try {
            $transaksi = Pemasukan::create([
                'tanggal' => $validated['tanggal'],
                'jumlah' => $validated['jumlah'],
                'keterangan' => $validated['keterangan'] ?? '',
                'jenis_transaksi' => $validated['jenis_pemasukan'],
                'siswa_id' => $validated['jenis_pemasukan'] === 'SPP' ? ($validated['siswa_id'] ?? null) : null,
                'bukti_transaksi' => $buktiPath,
                'id_admin' => $user->id,
            ]);

            // Create audit log
            $tanggalFormatted = Carbon::parse($transaksi->tanggal)->format('d-m-Y');
            $pesan = "Admin {$user->name} menambahkan transaksi pemasukan sebesar " . rupiah($transaksi->jumlah) . " pada {$tanggalFormatted}";

            AuditLog::create([
                'aktivitas' => $pesan,
                'tanggal' => now(),
                'id_admin' => $user->id,
                'id_transaksi' => $transaksi->id,
            ]);

            DB::commit();

            // Flash success message
            return redirect()->route('admin.pemasukan.index')
                ->with('success', 'Pemasukan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            // Optionally delete uploaded file on failure
            if ($buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }

            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan pemasukan.']);
        }
    }
}
