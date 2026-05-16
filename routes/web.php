<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AcademicCalendarController as AdminAcademicCalendarController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KhsController as AdminKhsController;
use App\Http\Controllers\Admin\KrsController as AdminKrsController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Admin\MataKuliahController as AdminMataKuliahController;
use App\Http\Controllers\Admin\PplController as AdminPplController;
use App\Http\Controllers\Admin\SkripsiController as AdminSkripsiController;
use App\Http\Controllers\Dosen\AcademicCalendarController as DosenAcademicCalendarController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\KrsApprovalController as DosenKrsApprovalController;
use App\Http\Controllers\Dosen\MahasiswaController as DosenMahasiswaController;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\Dosen\PplBimbinganController as DosenPplBimbinganController;
use App\Http\Controllers\Dosen\ProfilController as DosenProfilController;
use App\Http\Controllers\Dosen\SkripsiBimbinganController as DosenSkripsiBimbinganController;
use App\Http\Controllers\Mahasiswa\AcademicCalendarController as MahasiswaAcademicCalendarController;
use App\Http\Controllers\Mahasiswa\AbsensiController as MahasiswaAbsensiController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\BiodataPdfController;
use App\Http\Controllers\Mahasiswa\KhsController as MahasiswaKhsController;
use App\Http\Controllers\Mahasiswa\KrsController as MahasiswaKrsController;
use App\Http\Controllers\Mahasiswa\PplBimbinganController as MahasiswaPplBimbinganController;
use App\Http\Controllers\Mahasiswa\PplController as MahasiswaPplController;
use App\Http\Controllers\Mahasiswa\ProfilController as MahasiswaProfilController;
use App\Http\Controllers\Mahasiswa\SkripsiBimbinganController as MahasiswaSkripsiBimbinganController;
use App\Http\Controllers\Mahasiswa\SkripsiController as MahasiswaSkripsiController;
use App\Http\Controllers\Keuangan\PembayaranController as KeuanganPembayaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::any('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
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
        Route::delete('/mahasiswa/bulk-delete', [AdminMahasiswaController::class, 'bulkDestroy'])->name('mahasiswa.bulk-delete');
        Route::resource('mahasiswa', AdminMahasiswaController::class);

        Route::get('/dosen/export/pdf', [AdminDosenController::class, 'exportPdf'])->name('dosen.export.pdf');
        Route::get('/dosen/export/excel', [AdminDosenController::class, 'exportExcel'])->name('dosen.export.excel');
        Route::delete('/dosen/bulk-delete', [AdminDosenController::class, 'bulkDestroy'])->name('dosen.bulk-delete');
        Route::resource('dosen', AdminDosenController::class);

        Route::resource('mata-kuliah', AdminMataKuliahController::class)->except(['show']);
        Route::post('/mata-kuliah/{mataKuliah}/rps-admin', [AdminMataKuliahController::class, 'uploadRpsAdmin'])->name('mata-kuliah.rps-admin.upload');
        Route::get('/mata-kuliah/{mataKuliah}/rps-admin', [AdminMataKuliahController::class, 'downloadRpsAdmin'])->name('mata-kuliah.rps-admin.download');
        Route::get('/mata-kuliah/{mataKuliah}/rps-admin/preview', [AdminMataKuliahController::class, 'previewRpsAdmin'])->name('mata-kuliah.rps-admin.preview');
        Route::delete('/mata-kuliah/{mataKuliah}/rps-admin', [AdminMataKuliahController::class, 'destroyRpsAdmin'])->name('mata-kuliah.rps-admin.destroy');
        Route::get('/mata-kuliah/{mataKuliah}/rps-dosen', [AdminMataKuliahController::class, 'downloadRpsDosen'])->name('mata-kuliah.rps-dosen.download');
        Route::get('/mata-kuliah/{mataKuliah}/rps-dosen/preview', [AdminMataKuliahController::class, 'previewRpsDosen'])->name('mata-kuliah.rps-dosen.preview');
        Route::delete('/mata-kuliah/{mataKuliah}/rps-dosen', [AdminMataKuliahController::class, 'destroyRpsDosen'])->name('mata-kuliah.rps-dosen.destroy');

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
        Route::get('/absensi/rekap/pdf', [AdminAbsensiController::class, 'exportRekapPdf'])->name('absensi.rekap.pdf');

        Route::resource('kalender-akademik', AdminAcademicCalendarController::class)
            ->parameters(['kalender-akademik' => 'kalender_akademik'])
            ->except(['show']);

        Route::get('/skripsi', [AdminSkripsiController::class, 'index'])->name('skripsi.index');
        Route::get('/skripsi/{skripsi}', [AdminSkripsiController::class, 'show'])->name('skripsi.show');
        Route::patch('/skripsi/{skripsi}/status', [AdminSkripsiController::class, 'updateStatus'])->name('skripsi.status');
        Route::patch('/skripsi/{skripsi}/assign', [AdminSkripsiController::class, 'assign'])->name('skripsi.assign');
        Route::get('/skripsi/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'downloadSkPembimbing'])->name('skripsi.sk.download');
        Route::get('/skripsi/{skripsi}/sk-pembimbing/preview', [AdminSkripsiController::class, 'previewSkPembimbing'])->name('skripsi.sk.preview');
        Route::delete('/skripsi/{skripsi}', [AdminSkripsiController::class, 'destroy'])->name('skripsi.destroy');
        Route::delete('/skripsi/bulk-delete', [AdminSkripsiController::class, 'bulkDestroy'])->name('skripsi.bulk-delete');

        Route::get('/ppl', [AdminPplController::class, 'index'])->name('ppl.index');
        Route::get('/ppl/{ppl}', [AdminPplController::class, 'show'])->name('ppl.show');
        Route::patch('/ppl/{ppl}/status', [AdminPplController::class, 'updateStatus'])->name('ppl.status');
        Route::patch('/ppl/{ppl}/assign', [AdminPplController::class, 'assign'])->name('ppl.assign');
        Route::get('/ppl/{ppl}/sk-pembimbing', [AdminPplController::class, 'downloadSkPembimbing'])->name('ppl.sk.download');
        Route::get('/ppl/{ppl}/sk-pembimbing/preview', [AdminPplController::class, 'previewSkPembimbing'])->name('ppl.sk.preview');
        Route::delete('/ppl/{ppl}', [AdminPplController::class, 'destroy'])->name('ppl.destroy');
        Route::delete('/ppl/bulk-delete', [AdminPplController::class, 'bulkDestroy'])->name('ppl.bulk-delete');
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
        Route::get('/krs/{krs}/pdf', [MahasiswaKrsController::class, 'pdf'])->name('krs.pdf');
        Route::get('/krs/{krs}/edit', [MahasiswaKrsController::class, 'edit'])->name('krs.edit');
        Route::put('/krs/{krs}', [MahasiswaKrsController::class, 'update'])->name('krs.update');

        Route::get('/khs', [MahasiswaKhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/{khs}', [MahasiswaKhsController::class, 'show'])->name('khs.show');
        Route::get('/khs/{khs}/pdf', [MahasiswaKhsController::class, 'pdf'])->name('khs.pdf');

        Route::get('/absensi', [MahasiswaAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/{mataKuliah}/{semester}', [MahasiswaAbsensiController::class, 'show'])->name('absensi.show');
        Route::get('/absensi/{mataKuliah}/{semester}/pdf', [MahasiswaAbsensiController::class, 'pdf'])->name('absensi.pdf');

        Route::get('/pembayaran', [MahasiswaDashboardController::class, 'pembayaran'])->name('pembayaran.index');
        Route::post('/pembayaran/{pembayaran}/upload', [MahasiswaDashboardController::class, 'uploadPembayaran'])->name('pembayaran.upload');

        Route::get('/kalender-akademik', [MahasiswaAcademicCalendarController::class, 'index'])->name('kalender.index');

        Route::get('/skripsi', [MahasiswaSkripsiController::class, 'index'])->name('skripsi.index');
        Route::get('/skripsi/create', [MahasiswaSkripsiController::class, 'create'])->name('skripsi.create');
        Route::post('/skripsi', [MahasiswaSkripsiController::class, 'store'])->name('skripsi.store');
        Route::get('/skripsi/{skripsi}', [MahasiswaSkripsiController::class, 'show'])->name('skripsi.show');
        Route::get('/skripsi/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'downloadSkPembimbing'])->name('skripsi.sk.download');
        Route::get('/skripsi/{skripsi}/sk-pembimbing/preview', [AdminSkripsiController::class, 'previewSkPembimbing'])->name('skripsi.sk.preview');
        Route::get('/skripsi/{skripsi}/bimbingan', [MahasiswaSkripsiBimbinganController::class, 'show'])->name('skripsi.bimbingan');
        Route::post('/skripsi/{skripsi}/bimbingan', [MahasiswaSkripsiBimbinganController::class, 'store'])->name('skripsi.bimbingan.store');

        Route::get('/ppl', [MahasiswaPplController::class, 'index'])->name('ppl.index');
        Route::get('/ppl/create', [MahasiswaPplController::class, 'create'])->name('ppl.create');
        Route::post('/ppl', [MahasiswaPplController::class, 'store'])->name('ppl.store');
        Route::get('/ppl/{ppl}', [MahasiswaPplController::class, 'show'])->name('ppl.show');
        Route::get('/ppl/{ppl}/sk-pembimbing', [AdminPplController::class, 'downloadSkPembimbing'])->name('ppl.sk.download');
        Route::get('/ppl/{ppl}/sk-pembimbing/preview', [AdminPplController::class, 'previewSkPembimbing'])->name('ppl.sk.preview');
        Route::get('/ppl/{ppl}/bimbingan', [MahasiswaPplBimbinganController::class, 'show'])->name('ppl.bimbingan');
        Route::post('/ppl/{ppl}/bimbingan', [MahasiswaPplBimbinganController::class, 'store'])->name('ppl.bimbingan.store');

        Route::get('/biodata/pdf', BiodataPdfController::class)->name('biodata.pdf');
    });

Route::prefix('dosen')
    ->name('dosen.')
    ->middleware(['auth', 'role:dosen'])
    ->group(function () {
        Route::get('/dashboard', DosenDashboardController::class)->name('dashboard');

        Route::get('/mahasiswa', [DosenMahasiswaController::class, 'index'])->name('mahasiswa.index');

        Route::get('/nilai', [DosenNilaiController::class, 'index'])->name('nilai.index');
        Route::get('/nilai/{mataKuliah}/{semester}', [DosenNilaiController::class, 'edit'])->name('nilai.edit');
        Route::put('/nilai/{mataKuliah}/{semester}', [DosenNilaiController::class, 'update'])->name('nilai.update');

        Route::get('/mata-kuliah', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'index'])->name('mata-kuliah.index');
        Route::post('/mata-kuliah/{mataKuliah}/rps', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'uploadRps'])->name('mata-kuliah.rps.upload');
        Route::get('/mata-kuliah/{mataKuliah}/rps-admin', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'downloadRpsAdmin'])->name('mata-kuliah.rps-admin.download');
        Route::get('/mata-kuliah/{mataKuliah}/rps-admin/preview', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'previewRpsAdmin'])->name('mata-kuliah.rps-admin.preview');
        Route::get('/mata-kuliah/{mataKuliah}/rps-dosen', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'downloadRpsDosen'])->name('mata-kuliah.rps-dosen.download');
        Route::get('/mata-kuliah/{mataKuliah}/rps-dosen/preview', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'previewRpsDosen'])->name('mata-kuliah.rps-dosen.preview');
        Route::delete('/mata-kuliah/{mataKuliah}/rps', [\App\Http\Controllers\Dosen\MataKuliahController::class, 'destroyRps'])->name('mata-kuliah.rps.destroy');

        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/entry', [AdminAbsensiController::class, 'entry'])->name('absensi.entry');
        Route::post('/absensi/{absensi}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::get('/absensi/{absensi}/export/pdf', [AdminAbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        Route::get('/absensi/{absensi}/export/excel', [AdminAbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
        Route::get('/absensi/rekap/pdf', [AdminAbsensiController::class, 'exportRekapPdf'])->name('absensi.rekap.pdf');

        Route::get('/krs/approval', [DosenKrsApprovalController::class, 'index'])->name('krs.approval');
        Route::get('/krs/{krs}', [DosenKrsApprovalController::class, 'show'])->name('krs.show');
        Route::patch('/krs/{krs}', [DosenKrsApprovalController::class, 'updateStatus'])->name('krs.update');

        Route::get('/profil', [DosenProfilController::class, 'show'])->name('profil');
        Route::post('/profil', [DosenProfilController::class, 'update'])->name('profil.update');

        Route::get('/kalender-akademik', [DosenAcademicCalendarController::class, 'index'])->name('kalender.index');

        Route::get('/skripsi/bimbingan', [DosenSkripsiBimbinganController::class, 'index'])->name('skripsi.bimbingan.index');
        Route::get('/skripsi/{skripsi}/bimbingan', [DosenSkripsiBimbinganController::class, 'show'])->name('skripsi.bimbingan.show');
        Route::post('/skripsi/{skripsi}/bimbingan', [DosenSkripsiBimbinganController::class, 'store'])->name('skripsi.bimbingan.store');

        Route::get('/skripsi/pengajuan', [AdminSkripsiController::class, 'index'])->name('skripsi-pengajuan.index');
        Route::get('/skripsi/pengajuan/{skripsi}', [AdminSkripsiController::class, 'show'])->name('skripsi-pengajuan.show');
        Route::patch('/skripsi/pengajuan/{skripsi}/status', [AdminSkripsiController::class, 'updateStatus'])->name('skripsi-pengajuan.status');
        Route::get('/skripsi/pengajuan/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'downloadSkPembimbing'])->name('skripsi-pengajuan.sk.download');
        Route::get('/skripsi/pengajuan/{skripsi}/sk-pembimbing/preview', [AdminSkripsiController::class, 'previewSkPembimbing'])->name('skripsi-pengajuan.sk.preview');

        Route::get('/ppl/bimbingan', [DosenPplBimbinganController::class, 'index'])->name('ppl.bimbingan.index');
        Route::get('/ppl/{ppl}/bimbingan', [DosenPplBimbinganController::class, 'show'])->name('ppl.bimbingan.show');
        Route::post('/ppl/{ppl}/bimbingan', [DosenPplBimbinganController::class, 'store'])->name('ppl.bimbingan.store');

        Route::get('/ppl/pengajuan', [AdminPplController::class, 'index'])->name('ppl-pengajuan.index');
        Route::get('/ppl/pengajuan/{ppl}', [AdminPplController::class, 'show'])->name('ppl-pengajuan.show');
        Route::patch('/ppl/pengajuan/{ppl}/status', [AdminPplController::class, 'updateStatus'])->name('ppl-pengajuan.status');
        Route::get('/ppl/pengajuan/{ppl}/sk-pembimbing', [AdminPplController::class, 'downloadSkPembimbing'])->name('ppl-pengajuan.sk.download');
        Route::get('/ppl/pengajuan/{ppl}/sk-pembimbing/preview', [AdminPplController::class, 'previewSkPembimbing'])->name('ppl-pengajuan.sk.preview');
    });

Route::prefix('keuangan')
    ->name('keuangan.')
    ->middleware(['auth', 'role:keuangan,admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, '__invoke'])->name('dashboard');
        Route::get('pembayaran/export/pdf', [KeuanganPembayaranController::class, 'exportPdf'])->name('pembayaran.export.pdf');
        Route::get('pembayaran/{pembayaran}/pdf', [KeuanganPembayaranController::class, 'downloadPdf'])->name('pembayaran.pdf');
        Route::patch('pembayaran/{pembayaran}/detail/{detail}/status', [KeuanganPembayaranController::class, 'updateDetailStatus'])->name('pembayaran.detail.status');
        Route::delete('pembayaran/bulk-delete', [KeuanganPembayaranController::class, 'bulkDestroy'])->name('pembayaran.bulk-delete');
        Route::resource('pembayaran', KeuanganPembayaranController::class);
        Route::post('pembayaran/{pembayaran}/cicilan', [KeuanganPembayaranController::class, 'addCicilan'])->name('pembayaran.cicilan');
    });

require __DIR__.'/auth.php';
