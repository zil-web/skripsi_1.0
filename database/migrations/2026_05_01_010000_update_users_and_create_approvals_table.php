<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersAndCreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kepsek'])->default('admin')->after('email');
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi')->constrained('transaksis')->cascadeOnDelete();
            $table->foreignId('id_kepsek')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['approved', 'rejected']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
