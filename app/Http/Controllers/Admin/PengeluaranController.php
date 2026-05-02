<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengeluaranRequest;
use App\Models\AuditLog;
use App\Models\Siswa;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of pengeluaran transactions for the authenticated admin.
     *
     * Features:
     * - Ambil transaksi jenis 'pengeluaran' milik admin yang login
     * - Filter by tanggal range, status, dan search keterangan
     * - Hitung statistik: total_pengeluaran, total_pending, total_approved
     * - Pagination 10 per halaman
     * - Urutkan berdasarkan tanggal terbaru
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Base query: pengeluaran milik admin yang sedang login
        $query = Transaksi::query()
            ->where('jenis', 'pengeluaran')
            ->where('id_admin', $userId);

        // Filter by tanggal range (tanggal_dari, tanggal_sampai)
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $from = Carbon::parse($request->input('tanggal_dari'))->startOfDay();
            $to = Carbon::parse($request->input('tanggal_sampai'))->endOfDay();
            $query->whereBetween('tanggal', [$from, $to]);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search keterangan
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('keterangan', 'like', "%{$search}%");
        }

        // Hitung statistik
        $totalPengeluaran = $query->clone()->sum('jumlah');
        $totalPending = $query->clone()->where('status', 'pending')->sum('jumlah');
        $totalApproved = $query->clone()->where('status', 'approved')->sum('jumlah');

        // Ambil data dengan pagination, urutkan terbaru
        $pengeluarans = $query->orderByDesc('tanggal')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengeluaran.index', [
            'pengeluarans' => $pengeluarans,
            'totalPengeluaran' => $totalPengeluaran,
            'totalPending' => $totalPending,
            'totalApproved' => $totalApproved,
        ]);
    }

    /**
     * Show the form for creating a new pengeluaran transaction.
     *
     * Ambil daftar siswa untuk dropdown di form
     */
    public function create()
    {
        $siswas = Siswa::select('id', 'nama', 'kelas')->get();

        return view('admin.pengeluaran.create', [
            'siswas' => $siswas,
        ]);
    }

    /**
     * Store a newly created pengeluaran transaction in storage.
     *
     * Flow:
     * 1. Validasi data menggunakan StorePengeluaranRequest
     * 2. Upload bukti ke storage/app/public/bukti_transaksi
     * 3. Simpan transaksi dengan status 'pending' dan jenis 'pengeluaran'
     * 4. Simpan audit log dengan format: "Admin [nama] menambahkan pengeluaran..."
     * 5. Redirect dengan flash success message
     */
    public function store(StorePengeluaranRequest $request)
    {
        $validated = $request->validated();
        $userId = Auth::id();
        $admin = Auth::user();

        try {
            // Gunakan database transaction untuk memastikan konsistensi data
            DB::transaction(function () use ($validated, $userId, $admin) {
                // 1. Upload bukti transaksi
                $buktiPath = null;
                if ($validated['bukti_transaksi'] ?? false) {
                    $file = $validated['bukti_transaksi'];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $buktiPath = $file->storeAs('bukti_transaksi', $fileName, 'public');
                }

                // 2. Simpan data transaksi
                $transaksi = Transaksi::create([
                    'tanggal' => $validated['tanggal'],
                    'jenis' => 'pengeluaran',
                    'jumlah' => $validated['jumlah'],
                    'keterangan' => $validated['keterangan'],
                    'bukti_transaksi' => $buktiPath,
                    'status' => 'pending',
                    'id_admin' => $userId,
                    'id_siswa' => $validated['id_siswa'] ?? null,
                ]);

                // 3. Simpan audit log
                $aktivitas = sprintf(
                    'Admin %s menambahkan pengeluaran sebesar %s untuk %s',
                    $admin->name,
                    rupiah((int) $validated['jumlah']),
                    $validated['keterangan']
                );

                AuditLog::create([
                    'aktivitas' => $aktivitas,
                    'tanggal' => now(),
                    'id_admin' => $userId,
                    'id_transaksi' => $transaksi->id,
                ]);
            });

            // 4. Flash success message
            return redirect()
                ->route('admin.pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil disimpan dan menunggu persetujuan');
        } catch (\Exception $e) {
            // Rollback dan tampilkan error
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengeluaran: ' . $e->getMessage());
        }
    }
}
