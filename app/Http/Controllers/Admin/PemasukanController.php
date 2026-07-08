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
    public function sppCreate()
    {
        $siswas = Siswa::where('is_active', 1)
            ->orderBy('kelas')
            ->orderBy('nama')
            ->get();

        return view('admin.pemasukan.spp-create', compact('siswas'));
    }

    public function sppStore(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'siswa_list' => ['required', 'json'],
            'bukti_siswa' => ['required', 'array'],
            'bukti_siswa.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'siswa_list.required' => 'Pilih minimal 1 siswa.',
            'siswa_list.json' => 'Format data siswa tidak valid.',
            'bukti_siswa.required' => 'Bukti transaksi per siswa harus diisi.',
            'bukti_siswa.array' => 'Format bukti transaksi per siswa tidak valid.',
            'bukti_siswa.*.file' => 'Bukti transaksi harus berupa file.',
            'bukti_siswa.*.mimes' => 'Format bukti transaksi harus JPG, JPEG, PNG, atau PDF.',
            'bukti_siswa.*.max' => 'Ukuran bukti transaksi maksimal 2MB.',
        ]);

        try {
            $siswaList = json_decode($validated['siswa_list'], true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($siswaList) || count($siswaList) === 0) {
                return back()
                    ->withInput()
                    ->withErrors(['siswa_list' => 'Pilih minimal 1 siswa.']);
            }

            $buktiFiles = $request->file('bukti_siswa', []);
            foreach ($siswaList as $item) {
                $siswaId = $item['siswa_id'] ?? null;

                if (!$siswaId || !isset($buktiFiles[$siswaId])) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'bukti_siswa.' . $siswaId => 'Bukti transaksi wajib diisi untuk siswa yang dipilih.',
                        ]);
                }
            }

            $result = $this->storeBulkSppTransactions(
                $user,
                $validated['tanggal'],
                $validated['keterangan'] ?? '',
                null,
                $siswaList,
                false,
                $buktiFiles
            );

            if ($result instanceof \Illuminate\Http\Response || $result instanceof \Illuminate\Http\RedirectResponse || $result instanceof \Illuminate\Http\JsonResponse) {
                return $result;
            }

            return redirect()->route('admin.pemasukan.index')
                ->with('success', 'Pemasukan SPP berhasil ditambahkan.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan pemasukan SPP.']);
        }
    }

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
            return $this->storeBulkSppTransactions(
                $user,
                $request->input('tanggal'),
                $request->input('keterangan') ?? '',
                $buktiPath,
                is_string($request->input('siswa_list')) ? (json_decode($request->input('siswa_list'), true) ?: []) : (is_array($request->input('siswa_list')) ? $request->input('siswa_list') : []),
                true
            );
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

    private function storeBulkSppTransactions($user, string $tanggal, string $keterangan, ?string $buktiPath, array $siswaList, bool $expectsJson = false, array $buktiFiles = [])
    {
        foreach ($siswaList as $item) {
            if (!isset($item['siswa_id']) || !isset($item['jumlah'])) {
                return $this->bulkSppFailure($buktiPath, 'Format data siswa tidak valid.', $expectsJson);
            }

            if (!is_numeric($item['jumlah']) || intval($item['jumlah']) <= 0) {
                return $this->bulkSppFailure($buktiPath, 'Jumlah harus berupa angka > 0.', $expectsJson);
            }

            if (!Siswa::where('id', $item['siswa_id'])->exists()) {
                return $this->bulkSppFailure($buktiPath, 'Siswa tidak ditemukan: ' . $item['siswa_id'], $expectsJson);
            }
        }

        DB::beginTransaction();

        try {
            $count = 0;
            $storedBuktiPaths = [];

            foreach ($siswaList as $item) {
                $siswaId = $item['siswa_id'];
                $studentBuktiPath = $buktiPath;

                if (!empty($buktiFiles) && isset($buktiFiles[$siswaId]) && $buktiFiles[$siswaId]) {
                    $studentBuktiPath = $buktiFiles[$siswaId]->store('bukti_pemasukan', 'public');
                    $storedBuktiPaths[] = $studentBuktiPath;
                }

                $transaksi = Pemasukan::create([
                    'tanggal' => $tanggal,
                    'jumlah' => intval($item['jumlah']),
                    'keterangan' => $keterangan,
                    'jenis_transaksi' => 'SPP',
                    'siswa_id' => $siswaId,
                    'bukti_transaksi' => $studentBuktiPath,
                    'id_admin' => $user->id,
                ]);

                AuditLog::create([
                    'aktivitas' => "Admin {$user->name} menambahkan transaksi SPP untuk siswa ID {$siswaId} sebesar " . rupiah($transaksi->jumlah),
                    'tanggal' => now(),
                    'id_admin' => $user->id,
                    'id_transaksi' => $transaksi->id,
                ]);

                $count++;
            }

            DB::commit();

            if ($expectsJson) {
                return response()->json(['success' => true, 'count' => $count]);
            }

            return $count;
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            foreach ($storedBuktiPaths ?? [] as $storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            return $this->bulkSppFailure($buktiPath, 'Terjadi kesalahan saat menyimpan pemasukan.', $expectsJson, 500);
        }
    }

    private function bulkSppFailure(string $buktiPath, string $message, bool $expectsJson, int $status = 422)
    {
        if ($buktiPath) {
            Storage::disk('public')->delete($buktiPath);
        }

        if ($expectsJson) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return back()->withInput()->withErrors(['siswa_list' => $message]);
    }
}
