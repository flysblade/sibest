<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;

use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\BeasiswaController;
use App\Http\Controllers\Mahasiswa\PengajuanController;
use App\Http\Controllers\Mahasiswa\PengumumanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TelegramWebhookController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PenyeleksiController as AdminPenyeleksiController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;

use App\Http\Controllers\Penyeleksi\PengajuanController as PenyeleksiPengajuanController;
use App\Http\Controllers\Penyeleksi\DashboardController as PenyeleksiDashboardController;
use App\Http\Controllers\Penyeleksi\BeasiswaSayaController as PenyeleksiBeasiswaSayaController;

/*
|--------------------------------------------------------------------------
| AUTH & ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'mahasiswa'  => redirect()->route('mahasiswa.dashboard'),
            'admin'      => redirect()->route('admin.dashboard'),
            'penyeleksi' => redirect()->route('penyeleksi.dashboard'),
            default      => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

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

Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('telegram.webhook');

/*
|--------------------------------------------------------------------------
| PROFILE & SETTINGS (Semua Role)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/data', [ProfileController::class, 'updateData'])->name('profile.updateData');

    // Pengaturan
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/telegram/connect', [SettingsController::class, 'connectTelegram'])
        ->name('settings.telegram.connect');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.updatePreferences');
});


/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');

        // Beasiswa
        Route::get('/beasiswa', [BeasiswaController::class, 'index'])->name('beasiswa.index');
        Route::get('/beasiswa/{id}', [BeasiswaController::class, 'show'])->name('beasiswa.show');

        // Pengajuan
        Route::get('/beasiswa/{id}/ajukan', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/beasiswa/{id}/ajukan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::post('/beasiswa/{id}/kipk/ajukan', [PengajuanController::class, 'storeKipK'])->name('pengajuan.kipk.store');

        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{id}/upload', [PengajuanController::class, 'upload'])->name('pengajuan.upload');
        Route::post('/pengajuan/{id}/upload', [PengajuanController::class, 'uploadStore'])->name('pengajuan.upload.store');
        Route::get('/pengajuan/{id}/review', [PengajuanController::class, 'review'])->name('pengajuan.review');
        Route::post('/pengajuan/{id}/finalisasi', [PengajuanController::class, 'finalize'])->name('pengajuan.finalize');

        // Tracking & Riwayat
        Route::get('/tracking', [PengajuanController::class, 'tracking'])->name('tracking.index');
        Route::get('/riwayat', [PengajuanController::class, 'riwayat'])->name('riwayat.index');

        // Pengumuman Mahasiswa
        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
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
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Pengajuan
        Route::get('/pengajuan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{id}', [AdminPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{id}/status', [AdminPengajuanController::class, 'updateStatus'])->name('pengajuan.updateStatus');
        Route::post('/dokumen/{id}/verify', [AdminPengajuanController::class, 'verifyDokumen'])->name('dokumen.verify');

        // Kelola User
        Route::get('/user', [AdminUserController::class, 'index'])->name('user.index');
        Route::post('/user/{id}/update', [AdminUserController::class, 'update'])->name('user.update');
        Route::get('/user/create', [AdminUserController::class, 'create'])->name('user.create');
        Route::post('/user', [AdminUserController::class, 'store'])->name('user.store');
        Route::get('/user/{id}/edit', [AdminUserController::class, 'edit'])->name('user.edit');

        // Kelola Penyeleksi
        Route::get('/penyeleksi', [AdminPenyeleksiController::class, 'index'])->name('penyeleksi.index');
        Route::post('/penyeleksi/{userId}/assign', [AdminPenyeleksiController::class, 'assign'])->name('penyeleksi.assign');
        Route::post('/penyeleksi/{userId}/unassign', [AdminPenyeleksiController::class, 'unassign'])->name('penyeleksi.unassign');

        // Pengumuman Admin
        Route::get('/pengumuman', [AdminPengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/create', [AdminPengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [AdminPengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/pengumuman/{id}/edit', [AdminPengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::post('/pengumuman/{id}/update', [AdminPengumumanController::class, 'update'])->name('pengumuman.update');
        Route::post('/pengumuman/{id}/destroy', [AdminPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
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

        // Dashboard
        Route::get('/dashboard', [PenyeleksiDashboardController::class, 'index'])->name('dashboard');

        // Beasiswa Saya
        Route::get('/beasiswa-saya', [PenyeleksiBeasiswaSayaController::class, 'index'])->name('beasiswa-saya.index');

        // Pengajuan
        Route::get('/pengajuan', [PenyeleksiPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{id}', [PenyeleksiPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{id}/status', [PenyeleksiPengajuanController::class, 'updateStatus'])->name('pengajuan.updateStatus');

        // Verifikasi Dokumen
        Route::post('/dokumen/{id}/verify', [PenyeleksiPengajuanController::class, 'verifyDokumen'])->name('dokumen.verify');

        // Keputusan Tahapan
        Route::post('/tahapan/{id}/decide', [PenyeleksiPengajuanController::class, 'decideTahapan'])->name('tahapan.decide');
    });