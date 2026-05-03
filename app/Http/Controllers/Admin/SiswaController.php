<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\AuditLog;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    // ── INDEX ─────────────────────────────
    public function index(Request $request)
    {
        $query = Siswa::query();

        // Filter search NIK atau nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', '%'.$search.'%')
                  ->orWhere('nama', 'like', '%'.$search.'%')
                  ->orWhere('nama_orangtua', 
                            'like', '%'.$search.'%');
            });
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter status aktif
        if ($request->filled('status')) {
            $query->where(
                'is_active', 
                $request->status === 'aktif'
            );
        }

        // Hitung statistik
        $total_siswa    = Siswa::count();
        $total_aktif    = Siswa::where('is_active', true)->count();
        $total_nonaktif = Siswa::where('is_active', false)->count();

        // Daftar kelas unik untuk filter dropdown
        $daftar_kelas = Siswa::select('kelas')
                             ->distinct()
                             ->orderBy('kelas')
                             ->pluck('kelas');

        $siswas = $query->orderBy('nama')
                        ->paginate(10)
                        ->withQueryString();

        return view('admin.siswa.index', compact(
            'siswas',
            'total_siswa',
            'total_aktif',
            'total_nonaktif',
            'daftar_kelas'
        ));
    }

    // ── STORE ─────────────────────────────
    public function store(StoreSiswaRequest $request)
    {
        $siswa = Siswa::create($request->validated());

        // Catat audit log
        AuditLog::create([
            'aktivitas'    => 'Admin ' 
                . auth()->user()->name 
                . ' menambahkan data siswa ' 
                . $siswa->nama 
                . ' (NIK: ' . $siswa->nik . ')'
                . ' Kelas ' . $siswa->kelas,
            'tanggal'      => now(),
            'id_admin'     => auth()->id(),
            'id_transaksi' => null,
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 
                'Data siswa ' . $siswa->nama 
                . ' berhasil ditambahkan');
    }

    // ── UPDATE ────────────────────────────
    public function update(
        UpdateSiswaRequest $request, 
        Siswa $siswa
    ) {
        $siswa->update($request->validated());

        // Catat audit log
        AuditLog::create([
            'aktivitas'    => 'Admin ' 
                . auth()->user()->name 
                . ' mengubah data siswa ' 
                . $siswa->nama 
                . ' (NIK: ' . $siswa->nik . ')',
            'tanggal'      => now(),
            'id_admin'     => auth()->id(),
            'id_transaksi' => null,
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 
                'Data siswa ' . $siswa->nama 
                . ' berhasil diperbarui');
    }

    // ── DESTROY ───────────────────────────
    public function destroy(Siswa $siswa)
    {
        $nama = $siswa->nama;
        $nik  = $siswa->nik;

        $siswa->delete(); // soft delete

        // Catat audit log
        AuditLog::create([
            'aktivitas'    => 'Admin ' 
                . auth()->user()->name 
                . ' menghapus data siswa ' 
                . $nama 
                . ' (NIK: ' . $nik . ')',
            'tanggal'      => now(),
            'id_admin'     => auth()->id(),
            'id_transaksi' => null,
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 
                'Data siswa ' . $nama 
                . ' berhasil dihapus');
    }
}
