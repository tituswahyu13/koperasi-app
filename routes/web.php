<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralTransactionController;
use App\Http\Controllers\ClosingController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\SimulasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('anggota', AnggotaController::class);
    
    // Rute Penarikan Simpanan (HARUS DI ATAS resource agar tidak konflik)
    Route::get('/simpanan/withdraw', [SimpananController::class, 'withdraw'])->name('simpanan.withdraw');
    Route::post('/simpanan/withdraw', [SimpananController::class, 'processWithdrawal'])->name('simpanan.process_withdrawal');
    
    // Rute Penarikan Simpanan Massal BARU
    Route::get('/simpanan/mass-withdraw', [SimpananController::class, 'massWithdraw'])->name('simpanan.mass_withdraw');
    Route::post('/simpanan/mass-withdraw', [SimpananController::class, 'processMassWithdrawal'])->name('simpanan.process_mass_withdrawal');

    // Rute Simpanan Resource
    Route::resource('simpanan', SimpananController::class);
    
    // Rute Pinjaman
    Route::resource('pinjaman', PinjamanController::class);
    // Rute Pembayaran Pinjaman Manual
    Route::get('/pinjaman/{pinjaman}/pay', [PinjamanController::class, 'pay'])->name('pinjaman.pay');
    Route::post('/pinjaman/{pinjaman}/process-payment', [PinjamanController::class, 'processPayment'])->name('pinjaman.process_payment');

    // Rute Transaksi Operasional
    Route::resource('general_transactions', GeneralTransactionController::class);

    // Rute Tutup Bulan (Closing)
    Route::get('/tutup-bulan', [ClosingController::class, 'index'])->name('closing.index');
    Route::post('/tutup-bulan', [ClosingController::class, 'process'])->name('closing.process');

    Route::get('/simulasi', [SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('/simulasi/calculate', [SimulasiController::class, 'calculate'])->name('simulasi.calculate');

    // Rute Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/simpanan', [LaporanController::class, 'simpanan'])->name('laporan.simpanan');
    Route::get('/laporan/pinjaman', [LaporanController::class, 'pinjaman'])->name('laporan.pinjaman');
    Route::get('/laporan/arus-kas', [LaporanController::class, 'arusKas'])->name('laporan.arus-kas');
    Route::get('/laporan/neraca', [LaporanController::class, 'neraca'])->name('laporan.neraca');

    // Manajemen Akses (Role & Permission)
    Route::resource('roles', RoleController::class);
});


require __DIR__ . '/auth.php';