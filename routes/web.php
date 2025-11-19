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

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('anggota', AnggotaController::class);
    Route::resource('simpanan', SimpananController::class);
    
    // Rute Pinjaman
    Route::resource('pinjaman', PinjamanController::class);
    // Rute Pembayaran Pinjaman Manual BARU
    Route::get('/pinjaman/{pinjaman}/pay', [PinjamanController::class, 'pay'])->name('pinjaman.pay');
    Route::post('/pinjaman/{pinjaman}/process-payment', [PinjamanController::class, 'processPayment'])->name('pinjaman.process_payment');

    // Rute Tutup Bulan (Closing) - Menggunakan method 'process' yang sudah kita buat
    Route::get('/tutup-bulan', [ClosingController::class, 'index'])->name('closing.index');
    Route::post('/tutup-bulan', [ClosingController::class, 'process'])->name('closing.process');

    Route::get('/simulasi', [SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('/simulasi/calculate', [SimulasiController::class, 'calculate'])->name('simulasi.calculate');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/simpanan', [LaporanController::class, 'simpanan'])->name('laporan.simpanan');
    Route::get('/laporan/pinjaman', [LaporanController::class, 'pinjaman'])->name('laporan.pinjaman');
    Route::get('/laporan/arus-kas', [LaporanController::class, 'arusKas'])->name('laporan.arus-kas');
    Route::get('/laporan/neraca', [LaporanController::class, 'neraca'])->name('laporan.neraca');
});

require __DIR__ . '/auth.php';