<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;

use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\BeasiswaController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengumumanController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuanController;

use App\Http\Controllers\Penyeleksi\PengajuanController as PenyeleksiPengajuanController;


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisterController::class, 'register'])
    ->name('register.store');


/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])
            ->name('dashboard');

        // Beasiswa
        Route::get('/beasiswa', [BeasiswaController::class, 'index'])
            ->name('beasiswa.index');

        Route::get('/beasiswa/{id}', [BeasiswaController::class, 'show'])
            ->name('beasiswa.show');

        // Pengajuan
        Route::get('/beasiswa/{id}/ajukan', [PengajuanController::class, 'create'])
            ->name('pengajuan.create');

        Route::post('/beasiswa/{id}/ajukan', [PengajuanController::class, 'store'])
            ->name('pengajuan.store');

        Route::post('/beasiswa/{id}/kipk/ajukan', [PengajuanController::class, 'storeKipK'])
            ->name('pengajuan.kipk.store');

        Route::get('/pengajuan', [PengajuanController::class, 'index'])
            ->name('pengajuan.index');

        Route::get('/pengajuan/{id}/upload', [PengajuanController::class, 'upload'])
            ->name('pengajuan.upload');

        Route::post('/pengajuan/{id}/upload', [PengajuanController::class, 'uploadStore'])
            ->name('pengajuan.upload.store');

        Route::get('/pengajuan/{id}/review', [PengajuanController::class, 'review'])
            ->name('pengajuan.review');

        Route::post('/pengajuan/{id}/finalisasi', [PengajuanController::class, 'finalize'])
            ->name('pengajuan.finalize');

        // Tracking, Riwayat, Pengumuman
        Route::get('/tracking', [PengajuanController::class, 'tracking'])
            ->name('tracking.index');

        Route::get('/riwayat', [PengajuanController::class, 'riwayat'])
            ->name('riwayat.index');

        Route::get('/pengumuman', [PengumumanController::class, 'index'])
            ->name('pengumuman.index');
    });


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Pengajuan
        Route::get('/pengajuan', [AdminPengajuanController::class, 'index'])
            ->name('pengajuan.index');

        Route::get('/pengajuan/{id}', [AdminPengajuanController::class, 'show'])
            ->name('pengajuan.show');

        Route::post('/pengajuan/{id}/status', [AdminPengajuanController::class, 'updateStatus'])
            ->name('pengajuan.updateStatus');

        // Verifikasi dokumen
        Route::post('/dokumen/{id}/verify', [AdminPengajuanController::class, 'verifyDokumen'])
            ->name('dokumen.verify');
    });


/*
|--------------------------------------------------------------------------
| PENYELEKSI
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:penyeleksi'])
    ->prefix('penyeleksi')
    ->name('penyeleksi.')
    ->group(function () {

        // Dashboard (masih placeholder)
        Route::get('/dashboard', function () {
            return 'Dashboard Penyeleksi';
        })->name('dashboard');

        // Pengajuan
        Route::get('/pengajuan', [PenyeleksiPengajuanController::class, 'index'])
            ->name('pengajuan.index');

        Route::get('/pengajuan/{id}', [PenyeleksiPengajuanController::class, 'show'])
            ->name('pengajuan.show');

        // Keputusan tahapan
        Route::post('/tahapan/{id}/decide', [PenyeleksiPengajuanController::class, 'decideTahapan'])
            ->name('tahapan.decide');
    });