<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('edit_requests', function (Blueprint $table) {
            $table->id();
            
            // Reference to transaction being edited
            $table->foreignId('transaksi_id')
                ->constrained('transaksis')
                ->cascadeOnDelete();
            
            // Admin (bendahara) who requested the edit
            $table->foreignId('requested_by')
                ->constrained('users')
                ->cascadeOnDelete();
            
            // Old values (before proposed edit)
            $table->bigInteger('old_jumlah');
            $table->string('old_jenis');
            $table->text('old_keterangan')->nullable();
            
            // New proposed values (what bendahara wants to change to)
            $table->bigInteger('new_jumlah');
            $table->string('new_jenis');
            $table->text('new_keterangan')->nullable();
            
            // Approval status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Optional note from kepsek on rejection/approval
            $table->text('catatan_kepsek')->nullable();
            
            // Kepala Sekolah who reviewed this edit request
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            
            // When kepsek reviewed
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edit_requests');
    }
};
