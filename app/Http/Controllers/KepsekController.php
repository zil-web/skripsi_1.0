<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

// TAMBAHAN LIBRARY UNTUK EXCEL
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class KepsekController extends Controller
{
    private function currentReviewerId(): int|string|null
    {
        $userId = Auth::id();
        if ($userId) {
            return $userId;
        }

        $kepsek = Auth::guard('kepsek')->user();
        if (!$kepsek) {
            return null;
        }

        $linkedUser = User::firstOrCreate(
            ['username' => $kepsek->username],
            [
                'name' => $kepsek->nama,
                'email' => $kepsek->username . '@kepsek.local',
                'password' => bcrypt(Str::random(32)),
                'role' => 'kepsek',
            ]
        );

        if ($linkedUser->role !== 'kepsek') {
            $linkedUser->update(['role' => 'kepsek']);
        }

        \Log::debug('Kepsek reviewer lookup', [
            'kepsek_id' => $kepsek->id,
            'kepsek_username' => $kepsek->username,
            'auth_id' => $userId,
            'linked_user_id' => $linkedUser->id,
        ]);

        return $linkedUser->id;
    }

    public function index(): View
    {
        $pengeluaran = Transaksi::where('tipe', 'pengeluaran')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Exclude edit requests that belong to transactions which are currently
        // pending pengeluaran. This ensures pending pengeluaran appear first
        // in the approvals UI and duplicate edit-approve rows are not shown.
        $excludeIds = $pengeluaran->pluck('id')->all();

        $editRequests = EditRequest::with(['transaksi', 'requestedBy'])
            ->pending()
            ->latest()
            ->whereNotIn('transaksi_id', $excludeIds)
            ->get();

        return view('kepsek.approve-list', compact('editRequests', 'pengeluaran'));
    }

    public function approveEdit($id): RedirectResponse
    {
        $editRequest = EditRequest::with('transaksi')->findOrFail($id);

        if ($editRequest->status !== 'pending') {
            return back()->with('error', 
                'Permintaan edit tidak dapat disetujui. Status saat ini: ' . $editRequest->status);
        }

        if (!$editRequest->transaksi) {
            return back()->with('error', 'Transaksi tidak ditemukan atau telah dihapus.');
        }

        $hasChanges = ($editRequest->old_jumlah != $editRequest->new_jumlah) ||
                      ($editRequest->old_jenis != $editRequest->new_jenis) ||
                      ($editRequest->old_keterangan != $editRequest->new_keterangan);
        
        if (!$hasChanges) {
            return back()->with('warning', 
                'Tidak ada perubahan data yang signifikan untuk disetujui.');
        }

        try {
            $reviewedBy = $this->currentReviewerId();
            if (!$reviewedBy) {
                return back()->with('error', 'Akun Kepala Sekolah belum terhubung dengan user yang valid.');
            }

            $editRequest->transaksi->update([
                'jumlah' => $editRequest->new_jumlah,
                'jenis' => $editRequest->new_jenis,
                'keterangan' => $editRequest->new_keterangan,
            ]);

            $editRequest->update([
                'status' => 'approved',
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menyetujui edit transaksi ID ' . $editRequest->transaksi_id . 
                              ' (dari ' . $editRequest->old_jumlah . ' menjadi ' . $editRequest->new_jumlah . ')',
                'tanggal' => now(),
                'id_admin' => $reviewedBy,
                'id_transaksi' => $editRequest->transaksi_id,
            ]);

            return back()->with('success', 'Perubahan transaksi telah disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyetujui: ' . $e->getMessage());
        }
    }

    public function rejectEdit(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_kepsek' => 'required|string|min:10|max:300',
        ], [
            'catatan_kepsek.required' => 'Catatan penolakan wajib diisi',
            'catatan_kepsek.min' => 'Catatan minimal 10 karakter',
            'catatan_kepsek.max' => 'Catatan maksimal 300 karakter',
        ]);

        $editRequest = EditRequest::findOrFail($id);

        if ($editRequest->status !== 'pending') {
            return back()->with('error', 
                'Permintaan edit tidak dapat ditolak. Status saat ini: ' . $editRequest->status);
        }

        try {
            $editRequest->update([
                'status' => 'rejected',
                'catatan_kepsek' => $validated['catatan_kepsek'],
                'reviewed_by' => $this->currentReviewerId(),
                'reviewed_at' => now(),
            ]);

            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menolak edit transaksi ID ' . $editRequest->transaksi_id . 
                              ' - Alasan: ' . $validated['catatan_kepsek'],
                'tanggal' => now(),
                'id_admin' => $this->currentReviewerId(),
                'id_transaksi' => $editRequest->transaksi_id,
            ]);

            return back()->with('info', 'Permintaan edit ditolak dengan catatan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menolak: ' . $e->getMessage());
        }
    }

    public function approvePengeluaran($id): RedirectResponse
    {
        $transaksi = Transaksi::where('tipe', 'pengeluaran')->findOrFail($id);

        if ($transaksi->status->value !== 'pending') {
            return back()->with('error', 
                'Pengeluaran tidak dapat disetujui. Status saat ini: ' . $transaksi->status->value);
        }

        $reviewedBy = $this->currentReviewerId();
        if (!$reviewedBy) {
            return back()->with('error', 'Akun Kepala Sekolah belum terhubung dengan user yang valid.');
        }
        if ($transaksi->reviewed_by && $transaksi->reviewed_by == $reviewedBy) {
            return back()->with('error', 
                'Anda sudah melakukan approval untuk pengeluaran ini sebelumnya.');
        }

        try {
            $transaksi->update([
                'status' => 'approved',
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menyetujui pengeluaran ID ' . $transaksi->id . 
                              ' - Rp ' . number_format($transaksi->jumlah, 0, ',', '.'),
                'tanggal' => now(),
                'id_admin' => $reviewedBy,
                'id_transaksi' => $transaksi->id,
            ]);

            return back()->with('success', 'Pengeluaran telah disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectPengeluaran(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_kepsek' => 'required|string|min:10|max:300',
        ], [
            'catatan_kepsek.required' => 'Catatan penolakan wajib diisi',
            'catatan_kepsek.min' => 'Catatan minimal 10 karakter',
            'catatan_kepsek.max' => 'Catatan maksimal 300 karakter',
        ]);

        $transaksi = Transaksi::where('tipe', 'pengeluaran')->findOrFail($id);

        if ($transaksi->status->value !== 'pending') {
            return back()->with('error', 
                'Pengeluaran tidak dapat ditolak. Status saat ini: ' . $transaksi->status->value);
        }

        $reviewedBy = $this->currentReviewerId();
        if (!$reviewedBy) {
            return back()->with('error', 'Akun Kepala Sekolah belum terhubung dengan user yang valid.');
        }
        if ($transaksi->reviewed_by && $transaksi->reviewed_by == $reviewedBy) {
            return back()->with('error', 
                'Anda sudah melakukan approval untuk pengeluaran ini sebelumnya.');
        }

        try {
            $transaksi->update([
                'status' => 'rejected',
                'catatan_kepsek' => $validated['catatan_kepsek'],
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            \App\Models\AuditLog::create([
                'aktivitas' => 'Kepala Sekolah menolak pengeluaran ID ' . $transaksi->id . 
                              ' - Rp ' . number_format($transaksi->jumlah, 0, ',', '.') . 
                              ' - Alasan: ' . $validated['catatan_kepsek'],
                'tanggal' => now(),
                'id_admin' => $reviewedBy,
                'id_transaksi' => $transaksi->id,
            ]);

            return back()->with('info', 'Pengeluaran ditolak dengan catatan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

        // Hanya hitung transaksi yang sudah disetujui untuk ringkasan
        $statsQuery = clone $query;
        $total_pemasukan = (int) (clone $statsQuery)->where('jenis', 'pemasukan')->where('status', 'approved')->sum('jumlah');
        $total_pengeluaran = (int) (clone $statsQuery)->where('jenis', 'pengeluaran')->where('status', 'approved')->sum('jumlah');
        $saldo_bersih = $total_pemasukan - $total_pengeluaran;
        $total_count = $transaksis->total();

        return view('kepsek.transaksi', compact('transaksis', 'total_pemasukan', 'total_pengeluaran', 'saldo_bersih', 'total_count'));
    }

    public function availableDates(Request $request)
    {
        $dates = Transaksi::selectRaw('DATE(tanggal) as tanggal')
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        return response()->json($dates);
    }

    // METHOD SUDAH DIGANTI MENJADI EXPORT EXCEL (.XLSX)
    public function exportExcel(Request $request)
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

        // Warna tema manajemen keuangan sekolah
        $HIJAU_JUDUL   = '1B5E20';
        $HIJAU_HEADER  = '2E7D32';
        $HIJAU_TOTAL   = '388E3C';
        $HIJAU_RINGKAS = '388E3C';
        $KREM          = 'FFF8E7';
        $KREM_ALT      = 'FFFFFF';
        $MERAH         = 'B71C1C';
        $BIRU          = '1565C0';

        $scalar = static function ($value): string {
            if ($value instanceof \BackedEnum) return (string) $value->value;
            return (string) $value;
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Transaksi');

        $headers = [
            'No', 'Tanggal', 'Tipe', 'Jumlah (Rp)', 'Jenis', 'Keterangan',
            'Status', 'Nama Siswa', 'NIS Siswa', 'Dicatat Oleh',
            'Disetujui Oleh', 'Tanggal Disetujui', 'Catatan Penolakan',
        ];
        $widths  = [5, 13, 13, 16, 14, 28, 12, 20, 12, 18, 18, 16, 25];
        $numCols = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($numCols);

        // Set Lebar Kolom otomatis sesuai array
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        // Baris 1: Banner Judul Laporan Utama
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN SEKOLAH');
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $HIJAU_JUDUL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $HIJAU_JUDUL]]],
        ]);

        // Baris 2: Pembuatan Header Tabel
        $sheet->getRowDimension(2)->setRowHeight(22);
        foreach ($headers as $i => $h) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}2", $h);
        }
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $HIJAU_HEADER]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
        ]);

        // Baris Iterasi Data Transaksi
        $dataStartRow = 3;
        $totalPemasukan   = 0;
        $totalPengeluaran = 0;

        foreach ($transaksis as $i => $t) {
            $row         = $dataStartRow + $i;
            $statusValue = $scalar($t->status);
            $tipeValue   = $scalar($t->tipe);
            $jenisValue  = $scalar($t->jenis);
            $jumlah      = (float) ($t->jumlah ?? 0);
            $tipeNorm    = strtolower(trim($tipeValue));

            $statusNorm = strtolower(trim($statusValue));
            // Hanya hitung untuk ringkasan jika status sudah disetujui
            if ($statusNorm === 'approved') {
                if ($tipeNorm === 'pemasukan')   $totalPemasukan   += $jumlah;
                if ($tipeNorm === 'pengeluaran') $totalPengeluaran += $jumlah;
            }

            $rowData = [
                $i + 1,
                Carbon::parse($t->tanggal)->format('d/m/Y'),
                ucfirst($tipeValue ?: '-'),
                $jumlah,
                $jenisValue ?: '-',
                $t->keterangan ?? '-',
                ucfirst($statusValue ?: '-'),
                $t->siswa->nama ?? '-',
                $t->siswa->nis  ?? '-',
                $t->user->name  ?? '-',
                optional($t->reviewedBy)->name ?? '-',
                $t->reviewed_at ? Carbon::parse($t->reviewed_at)->format('d/m/Y H:i') : '-',
                $t->catatan_kepsek ?? '-',
            ];

            foreach ($rowData as $ci => $val) {
                $col = Coordinate::stringFromColumnIndex($ci + 1);
                $sheet->setCellValue("{$col}{$row}", $val);
            }

            // Zebra Striping latar baris (Krem muda & Putih bergantian)
            $fillColor = ($i % 2 === 0) ? $KREM : $KREM_ALT;
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Arial', 'color' => ['rgb' => '212121']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
            ]);

            // Formatting khusus Nomor & Nominal mata uang
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // Baris Total Utama (Menggunakan Formula Excel)
        $totalRow    = $dataStartRow + count($transaksis);
        $dataEndRow  = $totalRow - 1;

        $sheet->getRowDimension($totalRow)->setRowHeight(20);
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->setCellValue("D{$totalRow}", "=SUM(D{$dataStartRow}:D{$dataEndRow})");

        $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $HIJAU_HEADER]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
        ]);
        $sheet->getStyle("D{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Tabel Ringkasan Terpisah (Pemasukan, Pengeluaran & Saldo Bersih)
        $rs = $totalRow + 2; 

        $sheet->mergeCells("A{$rs}:D{$rs}");
        $sheet->setCellValue("A{$rs}", 'RINGKASAN KEUANGAN');
        $sheet->getRowDimension($rs)->setRowHeight(22);
        $sheet->getStyle("A{$rs}:D{$rs}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $HIJAU_RINGKAS]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
        ]);

        $rsH = $rs + 1;
        $sheet->mergeCells("A{$rsH}:B{$rsH}");
        $sheet->mergeCells("C{$rsH}:D{$rsH}");
        $sheet->setCellValue("A{$rsH}", 'Keterangan');
        $sheet->setCellValue("C{$rsH}", 'Jumlah (Rp)');
        $sheet->getRowDimension($rsH)->setRowHeight(18);
        $sheet->getStyle("A{$rsH}:D{$rsH}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $HIJAU_HEADER]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
        ]);

        $ringData = [
            ['Total Pemasukan (+)',   $totalPemasukan,              $HIJAU_JUDUL, false],
            ['Total Pengeluaran (-)', -$totalPengeluaran,           $MERAH,       false],
            ['Saldo Bersih',         $totalPemasukan - $totalPengeluaran, $BIRU,        true],
        ];

        foreach ($ringData as $offset => [$label, $nilai, $fontColor, $bold]) {
            $r = $rsH + 1 + $offset;
            $sheet->getRowDimension($r)->setRowHeight(18);

            $sheet->mergeCells("A{$r}:B{$r}");
            $sheet->mergeCells("C{$r}:D{$r}");
            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("C{$r}", $nilai);

            $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $KREM]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
            ]);
            $sheet->getStyle("A{$r}")->getFont()->setName('Arial')->setSize(10)->setBold($bold)->getColor()->setRGB($fontColor);
            $sheet->getStyle("C{$r}")->getFont()->setName('Arial')->setSize(10)->setBold($bold)->getColor()->setRGB($fontColor);
            $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("C{$r}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // Membekukan baris header agar tidak tergulung ke atas saat scroll down
        $sheet->freezePane('B3');

        // Penamaan file dinamis berdasarkan filter pencarian
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
        $filename .= '-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function transaksiDetail($id)
    {
        $transaksi = Transaksi::with(['siswa', 'user', 'reviewedBy'])
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id'              => $transaksi->id,
                'tanggal'         => optional($transaksi->tanggal)->format('d/m/Y'),
                'jenis'           => $transaksi->jenis instanceof \BackedEnum
                    ? $transaksi->jenis->value
                    : $transaksi->jenis,
                'jenis_transaksi' => ucfirst($transaksi->jenis instanceof \BackedEnum
                    ? $transaksi->jenis->value
                    : ($transaksi->jenis ?? '-')),
                'jumlah'          => $transaksi->jumlah,
                'keterangan'      => $transaksi->keterangan,
                'status'          => $transaksi->status instanceof \BackedEnum
                    ? $transaksi->status->value
                    : $transaksi->status,
                'siswa'           => $transaksi->siswa ? [
                    'nama'  => $transaksi->siswa->nama,
                    'nis'   => $transaksi->siswa->nis,
                    'kelas' => $transaksi->siswa->kelas,
                ] : null,
                'bukti_url'       => $transaksi->bukti_transaksi
                    ? asset('storage/' . $transaksi->bukti_transaksi)
                    : null,
                'bukti_raw'       => $transaksi->bukti_transaksi,
            ]);
        }

        return redirect()->route('kepsek.transaksi');
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