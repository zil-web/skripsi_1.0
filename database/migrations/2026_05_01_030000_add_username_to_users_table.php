<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
        });

        DB::table('users')
            ->whereNull('username')
            ->orWhere('username', '')
            ->orderBy('id')
            ->get()
            ->each(function ($user): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => 'user-' . $user->id]);
            });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'username')) {
                $table->unique('username');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }
        });
    }
};
