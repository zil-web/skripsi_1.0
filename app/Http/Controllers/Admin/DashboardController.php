<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $awalBulanIni = Carbon::now()->startOfMonth();
        $akhirBulanIni = Carbon::now()->endOfMonth();

        $total_pemasukan_bulan = (int) Transaksi::where('jenis', 'pemasukan')
            ->where('status', 'approved')
            ->whereBetween('tanggal', [$awalBulanIni, $akhirBulanIni])
            ->sum('jumlah');

        $total_pengeluaran_bulan = (int) Transaksi::where('jenis', 'pengeluaran')
            ->where('status', 'approved')
            ->whereBetween('tanggal', [$awalBulanIni, $akhirBulanIni])
            ->sum('jumlah');

        $saldo_bersih = $total_pemasukan_bulan - $total_pengeluaran_bulan;

        $total_pending = Transaksi::where('status', 'pending')->count();

        $labels = [];
        $seriesPemasukan = [];
        $seriesPengeluaran = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonthsNoOverflow($i);
            $year = $dt->year;
            $month = $dt->month;

            $labels[] = $dt->translatedFormat('M Y');

            $seriesPemasukan[] = (int) Transaksi::where('jenis', 'pemasukan')
                ->where('status', 'approved')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('jumlah');

            $seriesPengeluaran[] = (int) Transaksi::where('jenis', 'pengeluaran')
                ->where('status', 'approved')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('jumlah');
        }

        $data_grafik_6bulan = [
            'labels' => $labels,
            'pemasukan' => $seriesPemasukan,
            'pengeluaran' => $seriesPengeluaran,
        ];

        $status_transaksi_bulan_ini = [
            'approved' => Transaksi::whereBetween('tanggal', [$awalBulanIni, $akhirBulanIni])->where('status', 'approved')->count(),
            'pending' => Transaksi::whereBetween('tanggal', [$awalBulanIni, $akhirBulanIni])->where('status', 'pending')->count(),
            'rejected' => Transaksi::whereBetween('tanggal', [$awalBulanIni, $akhirBulanIni])->where('status', 'rejected')->count(),
        ];

        $transaksi_terbaru = Transaksi::with('siswa')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $audit_terbaru = AuditLog::with('transaksi')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'total_pemasukan_bulan',
            'total_pengeluaran_bulan',
            'saldo_bersih',
            'total_pending',
            'transaksi_terbaru',
            'audit_terbaru',
            'data_grafik_6bulan',
            'status_transaksi_bulan_ini'
        ));
    }
}
