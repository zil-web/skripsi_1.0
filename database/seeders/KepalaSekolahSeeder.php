<?php

namespace Database\Seeders;

use App\Models\KepalaSekolah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KepalaSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KepalaSekolah::create([
            'nama' => 'Kepala Sekolah',
            'nip' => '198512101012345',
            'username' => 'kepsek',
            'password' => Hash::make('12345678'),
            'foto' => null,
            'is_active' => true,
        ]);

        KepalaSekolah::create([
            'nama' => 'Dr. Fizi Hermawan, M.Pd',
            'nip' => '197805152005011001',
            'username' => 'fizi',
            'password' => Hash::make('12345678'),
            'foto' => null,
            'is_active' => true,
        ]);
    }
}
