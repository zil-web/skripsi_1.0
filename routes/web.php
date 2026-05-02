<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\PemasukanController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Kepsek\DashboardKepsekController;

// Home route: redirect based on guard
Route::get('/', function () {
    if (auth()->guard('web')->check()) {
        return redirect('/admin/dashboard');
    }
    if (auth()->guard('kepsek')->check()) {
        return redirect('/kepsek/dashboard');
    }
    return redirect()->route('login');
});

// Profile and dashboard examples (existing)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Unified auth routes (shared login page)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Shared logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Admin routes (protected by auth.admin middleware)
Route::middleware(['auth.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('transaksi', TransaksiController::class)->only(['index', 'create', 'store']);
    Route::get('pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
    Route::resource('pengeluaran', PengeluaranController::class)->only(['index', 'create', 'store']);
});

// Kepsek routes (protected by auth.kepsek middleware)
Route::middleware(['auth.kepsek'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('dashboard', [DashboardKepsekController::class, 'index'])->name('dashboard');
    // other kepsek routes can go here
});

// Approval routes (allow admin web guard or kepsek guard)
Route::middleware('auth:web,kepsek')->group(function () {
    Route::post('/approval/{id}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{id}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
});
