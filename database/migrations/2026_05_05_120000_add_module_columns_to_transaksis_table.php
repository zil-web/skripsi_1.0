<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksis', 'tipe')) {
                $table->string('tipe')->nullable()->after('id');
            }

            if (! Schema::hasColumn('transaksis', 'jenis_transaksi')) {
                $table->string('jenis_transaksi')->default('Lain-lain')->after('tipe');
            }

            if (! Schema::hasColumn('transaksis', 'siswa_id')) {
                $table->foreignId('siswa_id')->nullable()->after('jenis_transaksi')->constrained('siswas')->nullOnDelete();
            }

            if (! Schema::hasColumn('transaksis', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->nullable()->after('bukti_transaksi');
            }
        });

        DB::table('transaksis')
            ->whereNull('tipe')
            ->update([
                'tipe' => DB::raw("CASE WHEN jenis = 'pemasukan' THEN 'pemasukan' ELSE 'pengeluaran' END"),
            ]);

        DB::table('transaksis')
            ->whereNull('siswa_id')
            ->update([
                'siswa_id' => DB::raw('id_siswa'),
            ]);
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('transaksis', 'siswa_id')) {
                $table->dropConstrainedForeignId('siswa_id');
            }

            if (Schema::hasColumn('transaksis', 'jenis_transaksi')) {
                $table->dropColumn('jenis_transaksi');
            }

            if (Schema::hasColumn('transaksis', 'tipe')) {
                $table->dropColumn('tipe');
            }
        });
    }
};