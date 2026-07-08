<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Transaksi;

// Import library PhpSpreadsheet untuk Export Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi (Halaman Index)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Transaksi::with(['user', 'siswa', 'reviewedBy'])
            ->where('id_admin', $userId);

        // ── LOGIKA FILTER ──────────────────
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $from = Carbon::parse($request->input('tanggal_dari'))->startOfDay()->toDateString();
            $to = Carbon::parse($request->input('tanggal_sampai'))->endOfDay()->toDateString();
            $query->whereBetween('tanggal', [$from, $to]);
        }

        if ($request->filled('nominal')) {
            $nominal = (int) str_replace(['.', ','], '', (string) $request->input('nominal'));
            $query->where('jumlah', $nominal);
        }

        // Hanya hitung transaksi yang sudah disetujui untuk ringkasan
        $statsQuery = clone $query;
        $total_pemasukan = (int) (clone $statsQuery)->where('jenis', 'pemasukan')->where('status', 'approved')->sum('jumlah');
        $total_pengeluaran = (int) (clone $statsQuery)->where('jenis', 'pengeluaran')->where('status', 'approved')->sum('jumlah');
        $saldo_bersih = $total_pemasukan - $total_pengeluaran;

        $transaksis = $query->orderByDesc('tanggal')->paginate(10);
        $total_count = $transaksis->total();

        return view('admin.transaksi.index', compact('transaksis', 'total_pemasukan', 'total_pengeluaran', 'saldo_bersih', 'total_count'));
    }

    /**
     * Menampilkan detail dari satu transaksi (Resource Show)
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['user', 'siswa', 'reviewedBy'])
            ->where('id_admin', Auth::id())
            ->findOrFail($id);

        return view('admin.transaksi.show', compact('transaksi'));
    }

    /**
     * Menghapus data transaksi
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::where('id_admin', Auth::id())->findOrFail($id);
        $transaksi->delete();

        return redirect()->route('admin.transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Mengembalikan daftar tanggal yang memiliki transaksi untuk admin saat ini.
     * Digunakan oleh date picker agar hanya tanggal dengan data yang bisa dipilih.
     */
    public function availableDates(Request $request)
    {
        $userId = Auth::id();

        $dates = Transaksi::where('id_admin', $userId)
            ->selectRaw('DATE(tanggal) as tanggal')
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        return response()->json($dates);
    }

    public function detail($id)
    {
        $transaksi = Transaksi::where('id_admin', Auth::id())->findOrFail($id);
        return view('admin.transaksi.detail', compact('transaksi'));
    }

    public function bukti($id)
    {
        $transaksi = Transaksi::where('id_admin', Auth::id())->findOrFail($id);
        return view('admin.transaksi.bukti', compact('transaksi'));
    }

    /**
     * Method untuk menangani ekspor data ke Excel (PhpSpreadsheet)
     */
    public function exportExcel(Request $request)
    {
        $userId = Auth::id();

        $query = Transaksi::with(['user', 'siswa', 'reviewedBy'])
            ->where('id_admin', $userId);

        // ── LOGIKA FILTER ──────────────────
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->input('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $from = Carbon::parse($request->input('tanggal_dari'))->startOfDay()->toDateString();
            $to = Carbon::parse($request->input('tanggal_sampai'))->endOfDay()->toDateString();
            $query->whereBetween('tanggal', [$from, $to]);
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

        // ── LOGIKA PEMBUATAN EXCEL ──────────────────
        $HIJAU_JUDUL   = '1B5E20';
        $HIJAU_HEADER  = '2E7D32';
        $HIJAU_TOTAL   = '388E3C';
        $HIJAU_RINGKAS = '388E3C';
        $KREM          = 'FFF8E7';
        $KREM_ALT      = 'FFFFFF';

        $scalar = static function ($value): string {
            if ($value instanceof \BackedEnum) return (string) $value->value;
            return (string) $value;
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Transaksi');

        $headers = [
            'No','Tanggal','Tipe','Jumlah (Rp)','Jenis','Keterangan',
            'Status','Nama Siswa','NIS Siswa','Dicatat Oleh',
            'Disetujui Oleh','Tgl Disetujui','Catatan',
        ];
        $widths  = [5, 13, 13, 16, 14, 28, 12, 20, 12, 18, 18, 16, 25];
        $numCols = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($numCols);

        foreach ($widths as $i => $w) {
            $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }

        // Baris 1: Judul Laporan
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN');
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $HIJAU_JUDUL]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $HIJAU_JUDUL]]],
        ]);

        // Baris 2: Header Tabel
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

        // Baris Data
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
            // Hanya masukkan ke ringkasan jika transaksi sudah disetujui
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

            $fillColor = ($i % 2 === 0) ? $KREM : $KREM_ALT;
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Arial', 'color' => ['rgb' => '212121']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BDBDBD']]],
            ]);

            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // Baris Total Akumulasi
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

        // ── TABEL RINGKASAN KEUANGAN ──────────────────
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
            ['Total Pemasukan (+)',   $totalPemasukan,              '1B5E20', false],
            ['Total Pengeluaran (-)', -$totalPengeluaran,           'B71C1C', false],
            ['Saldo Bersih',         $totalPemasukan - $totalPengeluaran, '1565C0', true],
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

        $sheet->freezePane('B3');

        $filename = 'laporan-transaksi';
        if ($request->filled('tanggal_dari')) {
            $filename .= '-dari-' . $request->input('tanggal_dari');
        }
        if ($request->filled('tanggal_sampai')) {
            $filename .= '-sampai-' . $request->input('tanggal_sampai');
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
}