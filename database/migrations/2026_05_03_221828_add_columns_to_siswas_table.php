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
        Schema::table('siswas', function (Blueprint $table) {
            // Tambah NIS (NIS siswa 16 digit)
            if (!Schema::hasColumn('siswas', 'nis')) {
                $table->string('nis', 20)
                      ->unique()
                      ->nullable()
                      ->after('id')
                      ->comment('NIS siswa 16 digit');
            }

            // Tambah nama_orangtua
            if (!Schema::hasColumn('siswas', 'nama_orangtua')) {
                $table->string('nama_orangtua', 100)
                      ->nullable()
                      ->after('nama');
            }

            // Tambah jenis_kelamin
            if (!Schema::hasColumn('siswas', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])
                      ->nullable()
                      ->after('nama_orangtua')
                      ->comment('L = Laki-laki, P = Perempuan');
            }

            // Tambah alamat (jika belum ada)
            if (!Schema::hasColumn('siswas', 'alamat')) {
                $table->text('alamat')
                      ->nullable()
                      ->after('jenis_kelamin');
            }

            // Tambah no_telepon
            if (!Schema::hasColumn('siswas', 'no_telepon')) {
                $table->string('no_telepon', 15)
                      ->nullable()
                      ->after('alamat')
                      ->comment('No telepon orang tua');
            }

            // Tambah is_active
            if (!Schema::hasColumn('siswas', 'is_active')) {
                $table->boolean('is_active')
                      ->default(true)
                      ->after('no_telepon');
            }

            // Tambah soft deletes
            if (!Schema::hasColumn('siswas', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Hapus kolom dalam urutan kebalikan (untuk menghindari foreign key conflicts)
            $columns = ['deleted_at', 'is_active', 'no_telepon', 'alamat', 'jenis_kelamin', 'nama_orangtua', 'nis'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('siswas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

