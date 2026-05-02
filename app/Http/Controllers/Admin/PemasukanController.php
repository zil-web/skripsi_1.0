<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PemasukanController extends Controller
{
    /**
     * Display a listing of pemasukan (income transactions).
     *
     * Shows filtered list of transaksis with jenis='pemasukan' with pagination.
     */
    public function index(Request $request)
    {
        $query = Transaksi::where('jenis', 'pemasukan')
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

        $transaksis = $query->paginate(10);

        // Calculate total pemasukan (only approved)
        $total_pemasukan = Transaksi::where('jenis', 'pemasukan')
            ->where('status', 'approved')
            ->sum('jumlah');

        return view('admin.pemasukan.index', compact('transaksis', 'total_pemasukan'));
    }
}
