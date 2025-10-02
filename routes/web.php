<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClosingController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\SimulasiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// --- Rute yang dapat diakses oleh SEMUA USER yang sudah login (Anggota & Admin) ---
Route::middleware('auth')->group(function () {

    // Rute Dashboard (Bisa diakses oleh semua, tidak dilindungi oleh middleware 'admin')
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Simulasi Pinjaman
    Route::get('/simulasi', [SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('/simulasi/calculate', [SimulasiController::class, 'calculate'])->name('simulasi.calculate');
});


// --- Rute Admin Panel (Hanya dapat diakses oleh Role Admin) ---
Route::middleware(['auth', 'admin'])->group(function () {

    // Manajemen Anggota (CRUD)
    Route::resource('anggota', AnggotaController::class);

    // Manajemen Transaksi Simpanan
    Route::resource('simpanan', SimpananController::class);

    // Manajemen Pinjaman (Pengajuan, Persetujuan)
    Route::resource('pinjaman', PinjamanController::class);

    // Fitur Arus Kas dan Otomatisasi
    Route::get('/tutup-bulan', [ClosingController::class, 'index'])->name('closing.index');
    Route::post('/tutup-bulan', [ClosingController::class, 'closeMonth'])->name('closing.process');

    // Laporan Keuangan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/simpanan', [LaporanController::class, 'simpanan'])->name('laporan.simpanan');
    Route::get('/laporan/pinjaman', [LaporanController::class, 'pinjaman'])->name('laporan.pinjaman');
    Route::get('/laporan/arus-kas', [LaporanController::class, 'arusKas'])->name('laporan.arus-kas');
});

require __DIR__ . '/auth.php';
