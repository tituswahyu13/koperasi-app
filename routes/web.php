<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClosingController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Pindahkan ke sini

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('anggota', AnggotaController::class);
    Route::resource('simpanan', SimpananController::class);

    Route::get('/tutup-bulan', [ClosingController::class, 'index'])->name('closing.index');
    Route::post('/tutup-bulan', [ClosingController::class, 'closeMonth'])->name('closing.process');
});

require __DIR__ . '/auth.php';