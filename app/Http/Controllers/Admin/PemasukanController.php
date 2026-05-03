<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreTransaksiRequest;
use App\Models\AuditLog;
use App\Models\Siswa;
use App\Models\Transaksi;
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
        $query = Transaksi::where('jenis', 'pemasukan')
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by keterangan
        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        // Paginate results
        $pemasukkans = $query->paginate(10)->withQueryString();

        // Calculate total pemasukan (all, regardless of status)
        $totalPemasukan = $query->clone()->sum('jumlah');

        // Calculate pending amount
        $totalPending = $query->clone()->where('status', 'pending')->sum('jumlah');

        // Calculate approved amount
        $totalApproved = $query->clone()->where('status', 'approved')->sum('jumlah');

        // Get siswas for modal form
        $siswas = Siswa::select('id', 'nama', 'kelas')->get();

        return view('admin.pemasukan.index', compact('pemasukkans', 'totalPemasukan', 'totalPending', 'totalApproved', 'siswas'));
    }

    /**
     * Store a newly created pemasukan transaction in storage (via modal).
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
            return redirect()->route('admin.pemasukan.index')
                ->with('success', 'Pemasukan berhasil ditambahkan dan menunggu persetujuan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Optionally delete uploaded file on failure
            if ($buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }

            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan pemasukan.']);
        }
    }
}
