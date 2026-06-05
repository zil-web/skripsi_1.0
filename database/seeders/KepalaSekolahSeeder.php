<?php

namespace Database\Seeders;

use App\Models\KepalaSekolah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KepalaSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KepalaSekolah::updateOrCreate([
            'username' => 'kepsek',
        ], [
            'nama' => 'Kepala Sekolah',
            'nip' => '198512101012345',
            'username' => 'kepsek',
            'password' => '12345678',
            'foto' => null,
            'is_active' => true,
        ]);

        KepalaSekolah::updateOrCreate([
            'username' => 'fizi',
        ], [
            'nama' => 'Dr. Fizi Hermawan, M.Pd',
            'nip' => '197805152005011001',
            'username' => 'fizi',
            'password' => '12345678',
            'foto' => null,
            'is_active' => true,
        ]);
    }
}
