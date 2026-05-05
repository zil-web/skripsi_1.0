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
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'jenis_pemasukan' => ['required', 'in:SPP,Donasi,Dana BOS,Lain-lain'],
            'siswa_id' => ['required_if:jenis_pemasukan,SPP', 'nullable', 'exists:siswas,id'],
            'bukti_transaksi' => ['nullable', 'file', 'mimes:jpg,png,pdf', 'max:2048'],
        ]);

        $user = Auth::user();

        // Handle file upload if provided
        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti', 'public');
        }

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
