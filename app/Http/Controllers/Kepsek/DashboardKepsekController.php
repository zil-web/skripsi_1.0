<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Validasi;
use Carbon\Carbon;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardKepsekController extends Controller
{
    public function index(): View
    {
        $kepsek = Auth::guard('kepsek')->user();

        if (!$kepsek) {
            return redirect()->route('login');
        }

        $bulan_ini_mulai = Carbon::now()->startOfMonth();
        $bulan_ini_akhir = Carbon::now()->endOfMonth();

        $total_pemasukan_bulan = (int) Transaksi::where('jenis', 'pemasukan')
            ->where('status', 'approved')
            ->whereBetween('tanggal', [$bulan_ini_mulai, $bulan_ini_akhir])
            ->sum('jumlah');

        $total_pengeluaran_bulan = (int) Transaksi::where('jenis', 'pengeluaran')
            ->where('status', 'approved')
            ->whereBetween('tanggal', [$bulan_ini_mulai, $bulan_ini_akhir])
            ->sum('jumlah');

        $saldo_bersih = $total_pemasukan_bulan - $total_pengeluaran_bulan;

        $total_pending = Transaksi::where('status', 'pending')->count();

        $labels = [];
        $seriesPemasukan = [];
        $seriesPengeluaran = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonthsNoOverflow($i);
            $labels[] = $dt->translatedFormat('M Y');

            $seriesPemasukan[] = (int) Transaksi::where('jenis', 'pemasukan')
                ->where('status', 'approved')
                ->whereYear('tanggal', $dt->year)
                ->whereMonth('tanggal', $dt->month)
                ->sum('jumlah');

            $seriesPengeluaran[] = (int) Transaksi::where('jenis', 'pengeluaran')
                ->where('status', 'approved')
                ->whereYear('tanggal', $dt->year)
                ->whereMonth('tanggal', $dt->month)
                ->sum('jumlah');
        }

        $data_grafik_6bulan = [
            'labels' => $labels,
            'pemasukan' => $seriesPemasukan,
            'pengeluaran' => $seriesPengeluaran,
        ];

        $status_transaksi_bulan_ini = [
            'approved' => Transaksi::whereBetween('tanggal', [$bulan_ini_mulai, $bulan_ini_akhir])->where('status', 'approved')->count(),
            'pending' => Transaksi::whereBetween('tanggal', [$bulan_ini_mulai, $bulan_ini_akhir])->where('status', 'pending')->count(),
            'rejected' => Transaksi::whereBetween('tanggal', [$bulan_ini_mulai, $bulan_ini_akhir])->where('status', 'rejected')->count(),
        ];

        $menunggu_validasi = Transaksi::with('siswa')
            ->where('status', 'pending')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $riwayat_validasi = Validasi::with('transaksi')
            ->where('id_kepsek', $kepsek->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('kepsek.dashboard', [
            'kepsek' => $kepsek,
            'total_pemasukan_bulan' => $total_pemasukan_bulan,
            'total_pengeluaran_bulan' => $total_pengeluaran_bulan,
            'saldo_bersih' => $saldo_bersih,
            'total_pending' => $total_pending,
            'menunggu_validasi' => $menunggu_validasi,
            'riwayat_validasi' => $riwayat_validasi,
            'data_grafik_6bulan' => $data_grafik_6bulan,
            'status_transaksi_bulan_ini' => $status_transaksi_bulan_ini,
        ]);
    }
}
