<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransaksiRequest;
use App\Models\AuditLog;
use App\Models\Siswa;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    /**
     * Display a listing of all transactions (pemasukan + pengeluaran) for monitoring.
     *
     * Supports filters: tanggal_dari, tanggal_sampai, jenis, status, search on keterangan.
     * All transactions viewable (read-only).
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Base query: all transactions (both pemasukan and pengeluaran)
        $query = Transaksi::query()
            ->where('id_admin', $userId);

        // Filter by jenis (pemasukan / pengeluaran)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by tanggal range
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $from = Carbon::parse($request->input('tanggal_dari'))->startOfDay()->toDateString();
            $to = Carbon::parse($request->input('tanggal_sampai'))->endOfDay()->toDateString();
            $query->whereBetween('tanggal', [$from, $to]);
        }

        // Search keterangan
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('keterangan', 'like', "%{$search}%");
        }

        // Clone query for statistics
        $statsQuery = (clone $query);

        // Calculate statistics based on filter (only approved transactions)
        $total_pemasukan = $statsQuery->clone()->where('jenis', 'pemasukan')->where('status', 'approved')->sum('jumlah');
        $total_pengeluaran = $statsQuery->clone()->where('jenis', 'pengeluaran')->where('status', 'approved')->sum('jumlah');
        $saldo_bersih = $total_pemasukan - $total_pengeluaran;
        $total_count = $query->clone()->count();

        // Pagination 10 per page, sorted by latest
        $transaksis = $query->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.transaksi.index', compact(
            'transaksis',
            'total_pemasukan',
            'total_pengeluaran',
            'saldo_bersih',
            'total_count'
        ));
    }

    /**
     * Show the form for creating a new pemasukan transaction.
     *
     * Provides a list of siswa (id, nama, kelas) for the dropdown.
     */
    public function create()
    {
        $siswas = Siswa::select('id', 'nama', 'kelas')->get();

        return view('admin.transaksi.create', compact('siswas'));
    }

    /**
     * Store a newly created pemasukan transaction in storage.
     *
     * Uses StoreTransaksiRequest for validation, uploads bukti file if present,
     * creates the Transaksi with status 'pending' and logs an AuditLog entry.
     */
    public function store(StoreTransaksiRequest $request)
    {
        $user = Auth::user();

        // Handle file upload if provided
        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti', 'public');
        }

        // Wrap in DB transaction to ensure atomicity
        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'tanggal' => $request->input('tanggal'),
                'jenis' => 'pemasukan',
                'jumlah' => $request->input('jumlah'),
                'keterangan' => $request->input('keterangan'),
                'bukti_transaksi' => $buktiPath,
                'status' => 'pending',
                'id_admin' => $user->id,
                'id_siswa' => $request->input('id_siswa'),
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
            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Transaksi pemasukan berhasil ditambahkan dan menunggu persetujuan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Optionally delete uploaded file on failure
            if ($buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }

            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan transaksi.']);
        }
    }
}
