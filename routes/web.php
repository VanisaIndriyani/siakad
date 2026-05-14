<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KhsController as AdminKhsController;
use App\Http\Controllers\Admin\KrsController as AdminKrsController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Admin\MataKuliahController as AdminMataKuliahController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\MahasiswaController as DosenMahasiswaController;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\Dosen\ProfilController as DosenProfilController;
use App\Http\Controllers\Mahasiswa\AbsensiController as MahasiswaAbsensiController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\BiodataPdfController;
use App\Http\Controllers\Mahasiswa\KhsController as MahasiswaKhsController;
use App\Http\Controllers\Mahasiswa\KrsController as MahasiswaKrsController;
use App\Http\Controllers\Mahasiswa\ProfilController as MahasiswaProfilController;
use App\Http\Controllers\Keuangan\PembayaranController as KeuanganPembayaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'head'], '/', function () {
    return auth()->check()
        ? redirect()->to('/dashboard')
        : redirect()->to('/login');
});

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/mahasiswa/export/pdf', [AdminMahasiswaController::class, 'exportPdf'])->name('mahasiswa.export.pdf');
        Route::get('/mahasiswa/export/excel', [AdminMahasiswaController::class, 'exportExcel'])->name('mahasiswa.export.excel');
        Route::resource('mahasiswa', AdminMahasiswaController::class);

        Route::get('/dosen/export/pdf', [AdminDosenController::class, 'exportPdf'])->name('dosen.export.pdf');
        Route::get('/dosen/export/excel', [AdminDosenController::class, 'exportExcel'])->name('dosen.export.excel');
        Route::resource('dosen', AdminDosenController::class);

        Route::resource('mata-kuliah', AdminMataKuliahController::class)->except(['show']);

        Route::get('/krs', [AdminKrsController::class, 'index'])->name('krs.index');
        Route::get('/krs/{krs}', [AdminKrsController::class, 'show'])->name('krs.show');
        Route::patch('/krs/{krs}/status', [AdminKrsController::class, 'updateStatus'])->name('krs.status');

        Route::get('/khs', [AdminKhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/create', [AdminKhsController::class, 'create'])->name('khs.create');
        Route::post('/khs', [AdminKhsController::class, 'store'])->name('khs.store');
        Route::get('/khs/{khs}', [AdminKhsController::class, 'show'])->name('khs.show');
        Route::get('/khs/{khs}/edit', [AdminKhsController::class, 'edit'])->name('khs.edit');
        Route::put('/khs/{khs}', [AdminKhsController::class, 'update'])->name('khs.update');

        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/entry', [AdminAbsensiController::class, 'entry'])->name('absensi.entry');
        Route::post('/absensi/{absensi}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::get('/absensi/{absensi}/export/pdf', [AdminAbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        Route::get('/absensi/{absensi}/export/excel', [AdminAbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
    });

Route::prefix('mahasiswa')
    ->name('mahasiswa.')
    ->middleware(['auth', 'role:mahasiswa'])
    ->group(function () {
        Route::get('/dashboard', MahasiswaDashboardController::class)->name('dashboard');

        Route::get('/profil', [MahasiswaProfilController::class, 'show'])->name('profil');
        Route::post('/profil', [MahasiswaProfilController::class, 'update'])->name('profil.update');

        Route::get('/krs', [MahasiswaKrsController::class, 'index'])->name('krs.index');
        Route::get('/krs/create', [MahasiswaKrsController::class, 'create'])->name('krs.create');
        Route::post('/krs', [MahasiswaKrsController::class, 'store'])->name('krs.store');
        Route::get('/krs/{krs}', [MahasiswaKrsController::class, 'show'])->name('krs.show');
        Route::get('/krs/{krs}/edit', [MahasiswaKrsController::class, 'edit'])->name('krs.edit');
        Route::put('/krs/{krs}', [MahasiswaKrsController::class, 'update'])->name('krs.update');

        Route::get('/khs', [MahasiswaKhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/{khs}', [MahasiswaKhsController::class, 'show'])->name('khs.show');

        Route::get('/absensi', [MahasiswaAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/{mataKuliah}/{semester}', [MahasiswaAbsensiController::class, 'show'])->name('absensi.show');

        Route::get('/pembayaran', [MahasiswaDashboardController::class, 'pembayaran'])->name('pembayaran.index');

        Route::get('/biodata/pdf', BiodataPdfController::class)->name('biodata.pdf');
    });

Route::prefix('dosen')
    ->name('dosen.')
    ->middleware(['auth', 'role:dosen'])
    ->group(function () {
        Route::get('/dashboard', DosenDashboardController::class)->name('dashboard');

        Route::get('/mahasiswa', [DosenMahasiswaController::class, 'index'])->name('mahasiswa.index');

        Route::get('/nilai', [DosenNilaiController::class, 'index'])->name('nilai.index');
        Route::get('/nilai/{krs}', [DosenNilaiController::class, 'edit'])->name('nilai.edit');
        Route::put('/nilai/{krs}', [DosenNilaiController::class, 'update'])->name('nilai.update');

        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/entry', [AdminAbsensiController::class, 'entry'])->name('absensi.entry');
        Route::post('/absensi/{absensi}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::get('/absensi/{absensi}/export/pdf', [AdminAbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        Route::get('/absensi/{absensi}/export/excel', [AdminAbsensiController::class, 'exportExcel'])->name('absensi.export.excel');

        Route::get('/profil', [DosenProfilController::class, 'show'])->name('profil');
        Route::post('/profil', [DosenProfilController::class, 'update'])->name('profil.update');
    });

Route::prefix('keuangan')
    ->name('keuangan.')
    ->middleware(['auth', 'role:keuangan,admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, '__invoke'])->name('dashboard');
        Route::resource('pembayaran', KeuanganPembayaranController::class);
        Route::post('pembayaran/{pembayaran}/cicilan', [KeuanganPembayaranController::class, 'addCicilan'])->name('pembayaran.cicilan');
    });

require __DIR__.'/auth.php';
