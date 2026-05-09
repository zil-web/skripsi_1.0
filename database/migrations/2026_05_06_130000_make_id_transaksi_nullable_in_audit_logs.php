<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing foreign key then make column nullable, then re-add FK
        Schema::table('audit_logs', function (Blueprint $table) {
            // attempt to drop foreign key if exists
            try {
                $table->dropForeign(['id_transaksi']);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // Modify column to nullable (use raw statement to avoid requiring doctrine/dbal)
        DB::statement('ALTER TABLE `audit_logs` MODIFY `id_transaksi` BIGINT UNSIGNED NULL');

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('id_transaksi')->references('id')->on('transaksis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            try {
                $table->dropForeign(['id_transaksi']);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // Make column NOT NULL again
        DB::statement('ALTER TABLE `audit_logs` MODIFY `id_transaksi` BIGINT UNSIGNED NOT NULL');

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('id_transaksi')->references('id')->on('transaksis')->onDelete('cascade');
        });
    }
};
