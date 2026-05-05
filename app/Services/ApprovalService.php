<?php

namespace App\Services;

use App\Models\Transaksi;
use Carbon\Carbon;

class ApprovalService
{
    public function determineStatus(array $data, int $userId): string
    {
        $jenisPengeluaran = (string) ($data['jenis_pengeluaran'] ?? '');
        $jumlah = (float) ($data['jumlah'] ?? 0);
        $keterangan = (string) ($data['keterangan'] ?? '');

        // Pengeluaran di bawah 500.000 langsung approved (tanpa approval kepsek).
        if ($jumlah < 500000) {
            return 'approved';
        }

        $alwaysPendingTypes = [
            'Pembelian Aset',
            'Renovasi',
            'Kegiatan Besar',
        ];

        $autoApprovedTypes = [
            'ATK',
            'Konsumsi Harian',
            'Lain-lain',
        ];

        if (in_array($jenisPengeluaran, $alwaysPendingTypes, true)) {
            return 'pending';
        }

        if (! in_array($jenisPengeluaran, $autoApprovedTypes, true)) {
            return 'pending';
        }

        if ($jumlah > 1000000) {
            return 'pending';
        }

        if ($this->hasMicroTransactionPattern($userId)) {
            return 'pending';
        }

        if ($this->isNewVendorPattern($keterangan)) {
            return 'pending';
        }

        return 'approved';
    }

    private function hasMicroTransactionPattern(int $userId): bool
    {
        $today = Carbon::today();

        $microTransactionCount = Transaksi::query()
            ->where('jenis', 'pengeluaran')
            ->where('id_admin', $userId)
            ->whereDate('tanggal', $today)
            ->where('jumlah', '<', 200000)
            ->count();

        return $microTransactionCount >= 3;
    }

    private function isNewVendorPattern(string $newKeterangan): bool
    {
        $newWords = $this->normalizeWords($newKeterangan);

        if (count($newWords) < 3) {
            return true;
        }

        $existingKeterangans = Transaksi::query()
            ->where('jenis', 'pengeluaran')
            ->whereNotNull('keterangan')
            ->pluck('keterangan');

        foreach ($existingKeterangans as $existingKeterangan) {
            $existingWords = $this->normalizeWords((string) $existingKeterangan);

            if (count(array_intersect($newWords, $existingWords)) >= 3) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    private function normalizeWords(string $text): array
    {
        $words = preg_split('/[^\pL\pN]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $words = array_filter($words, static function (string $word): bool {
            return mb_strlen($word) >= 3;
        });

        return array_values(array_unique($words));
    }
}