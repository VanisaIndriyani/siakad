<?php

use App\Http\Controllers\Admin\CutiApprovalController as AdminCutiApprovalController;
use App\Http\Controllers\Dosen\CutiApprovalController as DosenCutiApprovalController;
use App\Http\Controllers\Mahasiswa\CutiController as MahasiswaCutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AcademicCalendarController as AdminAcademicCalendarController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KhsController as AdminKhsController;
use App\Http\Controllers\Admin\KrsController as AdminKrsController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Admin\MataKuliahController as AdminMataKuliahController;
use App\Http\Controllers\Admin\PengajuanLaporanController as AdminPengajuanLaporanController;
use App\Http\Controllers\Admin\PplController as AdminPplController;
use App\Http\Controllers\Admin\SkripsiController as AdminSkripsiController;
use App\Http\Controllers\Admin\KknController as AdminKknController;
use App\Http\Controllers\Mahasiswa\KknController as MahasiswaKknController;
use App\Http\Controllers\Dosen\KknController as DosenKknController;
use App\Http\Controllers\KknBimbinganController;
use App\Http\Controllers\Dosen\AcademicCalendarController as DosenAcademicCalendarController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\KrsApprovalController as DosenKrsApprovalController;
use App\Http\Controllers\Dosen\MahasiswaController as DosenMahasiswaController;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\Dosen\PengajuanLaporanController as DosenPengajuanLaporanController;
use App\Http\Controllers\Dosen\PplBimbinganController as DosenPplBimbinganController;
use App\Http\Controllers\Dosen\PplRevisiController as DosenPplRevisiController;
use App\Http\Controllers\Dosen\ProfilController as DosenProfilController;
use App\Http\Controllers\Dosen\SkripsiBimbinganController as DosenSkripsiBimbinganController;
use App\Http\Controllers\Dosen\SkripsiRevisiController as DosenSkripsiRevisiController;
use App\Http\Controllers\Mahasiswa\AcademicCalendarController as MahasiswaAcademicCalendarController;
use App\Http\Controllers\Mahasiswa\AbsensiController as MahasiswaAbsensiController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\BiodataPdfController;
use App\Http\Controllers\Mahasiswa\KhsController as MahasiswaKhsController;
use App\Http\Controllers\Mahasiswa\KrsController as MahasiswaKrsController;
use App\Http\Controllers\Mahasiswa\PengajuanLaporanController as MahasiswaPengajuanLaporanController;
use App\Http\Controllers\Mahasiswa\PplBimbinganController as MahasiswaPplBimbinganController;
use App\Http\Controllers\Mahasiswa\PplController as MahasiswaPplController;
use App\Http\Controllers\Mahasiswa\PplFileController as MahasiswaPplFileController;
use App\Http\Controllers\Mahasiswa\PplRevisiController as MahasiswaPplRevisiController;
use App\Http\Controllers\Mahasiswa\ProfilController as MahasiswaProfilController;
use App\Http\Controllers\Mahasiswa\SkripsiBimbinganController as MahasiswaSkripsiBimbinganController;
use App\Http\Controllers\Mahasiswa\SkripsiController as MahasiswaSkripsiController;
use App\Http\Controllers\Mahasiswa\SkripsiFileController as MahasiswaSkripsiFileController;
use App\Http\Controllers\Mahasiswa\SkripsiRevisiController as MahasiswaSkripsiRevisiController;
use App\Http\Controllers\Keuangan\PembayaranController as KeuanganPembayaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Route::any('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create();
    $added = [];

    foreach (app('router')->getRoutes()->getRoutes() as $route) {
        $methods = $route->methods();
        if (!in_array('GET', $methods, true) && !in_array('HEAD', $methods, true)) {
            continue;
        }

        $uri = $route->uri();
        if ($uri === 'sitemap.xml') {
            continue;
        }

        if (Str::contains($uri, '{')) {
            continue;
        }

        if (Str::startsWith($uri, ['_ignition', '_debugbar', 'telescope', 'horizon'])) {
            continue;
        }

        $middleware = $route->gatherMiddleware();
        $isProtected = false;
        foreach ($middleware as $m) {
            $m = (string) $m;
            if ($m === 'auth' || Str::startsWith($m, ['auth:', 'verified', 'role:', 'permission:'])) {
                $isProtected = true;
                break;
            }
        }
        if ($isProtected) {
            continue;
        }

        $url = url($uri === '/' ? '/' : $uri);
        if (isset($added[$url])) {
            continue;
        }
        $added[$url] = true;

        $sitemap->add(Url::create($url));
    }

    return $sitemap->toResponse(request());
})->name('sitemap');

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
        
        // User Management
        Route::get('/user', [AdminUserController::class, 'index'])->name('user.index');
        Route::get('/user/pdf', [AdminUserController::class, 'exportPdf'])->name('user.pdf');

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

        Route::delete('/krs/bulk-delete', [AdminKrsController::class, 'bulkDestroy'])->name('krs.bulk-delete');
        Route::get('/krs', [AdminKrsController::class, 'index'])->name('krs.index');
        Route::get('/krs/{krs}', [AdminKrsController::class, 'show'])->name('krs.show');
        Route::get('/krs/{krs}/pdf', [AdminKrsController::class, 'downloadPdf'])->name('krs.pdf');
        Route::patch('/krs/{krs}/status', [AdminKrsController::class, 'updateStatus'])->name('krs.status');
        Route::delete('/krs/{krs}', [AdminKrsController::class, 'destroy'])->name('krs.destroy');

        Route::get('/cuti', [AdminCutiApprovalController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/{cuti}', [AdminCutiApprovalController::class, 'show'])->name('cuti.show');
        Route::get('/cuti/{cuti}/pdf', [MahasiswaCutiController::class, 'downloadPdf'])->name('cuti.pdf');
        Route::patch('/cuti/{cuti}/status', [AdminCutiApprovalController::class, 'updateStatus'])->name('cuti.status');
        Route::delete('/cuti/bulk-delete', [AdminCutiApprovalController::class, 'bulkDestroy'])->name('cuti.bulk-delete');

        Route::delete('/khs/bulk-delete', [AdminKhsController::class, 'bulkDestroy'])->name('khs.bulk-delete');
        Route::get('/khs', [AdminKhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/create', [AdminKhsController::class, 'create'])->name('khs.create');
        Route::get('/khs/{khs}', [AdminKhsController::class, 'show'])->name('khs.show');
        Route::get('/khs/{khs}/pdf', [AdminKhsController::class, 'downloadPdf'])->name('khs.pdf');
        Route::post('/khs', [AdminKhsController::class, 'store'])->name('khs.store');
        Route::get('/khs/{khs}/edit', [AdminKhsController::class, 'edit'])->name('khs.edit');
        Route::put('/khs/{khs}', [AdminKhsController::class, 'update'])->name('khs.update');
        Route::delete('/khs/{khs}', [AdminKhsController::class, 'destroy'])->name('khs.destroy');

        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/manual', [AdminAbsensiController::class, 'downloadManual'])->name('absensi.manual');
        Route::get('/absensi/rekap', [AdminAbsensiController::class, 'exportRekapPdf'])->name('absensi.rekap');
        Route::get('/absensi/entry', [AdminAbsensiController::class, 'entry'])->name('absensi.entry');
        Route::post('/absensi/{absensi}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::get('/absensi/{absensi}/export/pdf', [AdminAbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        Route::get('/absensi/{absensi}/materi', [AdminAbsensiController::class, 'materiFile'])->name('absensi.materi');
        Route::delete('/absensi/{absensi}/materi', [AdminAbsensiController::class, 'destroyMateriFile'])->name('absensi.materi.destroy');
        Route::get('/absensi/{absensi}/export/excel', [AdminAbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
        Route::get('/absensi/rekap', [AdminAbsensiController::class, 'exportRekapPdf'])->name('absensi.rekap');

        Route::resource('kalender-akademik', AdminAcademicCalendarController::class)
            ->parameters(['kalender-akademik' => 'kalender_akademik'])
            ->except(['show']);
        Route::get('/kalender-akademik/pdf', [AdminAcademicCalendarController::class, 'pdf'])->name('kalender-akademik.pdf');

        Route::get('/skripsi', [AdminSkripsiController::class, 'index'])->name('skripsi.index');
        Route::get('/skripsi/{skripsi}', [AdminSkripsiController::class, 'show'])->name('skripsi.show');
        Route::get('/skripsi/{skripsi}/pdf', [AdminSkripsiController::class, 'downloadPdf'])->name('skripsi.pdf');
        Route::patch('/skripsi/{skripsi}/status', [AdminSkripsiController::class, 'updateStatus'])->name('skripsi.status');
        Route::patch('/skripsi/{skripsi}/assign', [AdminSkripsiController::class, 'assign'])->name('skripsi.assign');
        Route::get('/skripsi/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'downloadSkPembimbing'])->name('skripsi.sk.download');
        Route::get('/skripsi/{skripsi}/sk-pembimbing/preview', [AdminSkripsiController::class, 'previewSkPembimbing'])->name('skripsi.sk.preview');
        Route::delete('/skripsi/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'destroySkPembimbing'])->name('skripsi.sk.destroy');
        Route::delete('/skripsi/{skripsi}/pembimbing', [AdminSkripsiController::class, 'resetPembimbing'])->name('skripsi.pembimbing.reset');
        Route::delete('/skripsi/bulk-delete', [AdminSkripsiController::class, 'bulkDestroy'])->name('skripsi.bulk-delete');
        Route::delete('/skripsi/{skripsi}', [AdminSkripsiController::class, 'destroy'])->name('skripsi.destroy');
        Route::delete('/skripsi-files/{file}', [AdminSkripsiController::class, 'destroyFile'])->name('skripsi-files.destroy');

        Route::get('/ppl', [AdminPplController::class, 'index'])->name('ppl.index');
        Route::get('/ppl/{ppl}', [AdminPplController::class, 'show'])->name('ppl.show');
        Route::get('/ppl/{ppl}/pdf', [AdminPplController::class, 'downloadPdf'])->name('ppl.pdf');
        Route::patch('/ppl/{ppl}/status', [AdminPplController::class, 'updateStatus'])->name('ppl.status');
        Route::patch('/ppl/{ppl}/assign', [AdminPplController::class, 'assign'])->name('ppl.assign');
        Route::get('/ppl/{ppl}/sk-pembimbing', [AdminPplController::class, 'downloadSkPembimbing'])->name('ppl.sk.download');
        Route::get('/ppl/{ppl}/sk-pembimbing/preview', [AdminPplController::class, 'previewSkPembimbing'])->name('ppl.sk.preview');
        Route::delete('/ppl/bulk-delete', [AdminPplController::class, 'bulkDestroy'])->name('ppl.bulk-delete');
        Route::delete('/ppl/{ppl}', [AdminPplController::class, 'destroy'])->name('ppl.destroy');
        Route::delete('/ppl-files/{file}', [AdminPplController::class, 'destroyFile'])->name('ppl-files.destroy');

        // KKN Management
        Route::get('/kkn', [AdminKknController::class, 'index'])->name('kkn.index');
        Route::delete('/kkn/bulk-delete', [AdminKknController::class, 'bulkDestroy'])->name('kkn.bulk-delete');
        Route::patch('/kkn/{kkn}/status', [AdminKknController::class, 'updateStatus'])->name('kkn.status');
        Route::get('/kkn/posko', [AdminKknController::class, 'poskoIndex'])->name('kkn.posko.index');
        Route::get('/kkn/posko/create', [AdminKknController::class, 'poskoCreate'])->name('kkn.posko.create');
        Route::post('/kkn/posko', [AdminKknController::class, 'poskoStore'])->name('kkn.posko.store');
        Route::get('/kkn/posko/{posko}', [AdminKknController::class, 'poskoShow'])->name('kkn.posko.show');
        Route::put('/kkn/posko/{posko}', [AdminKknController::class, 'poskoUpdate'])->name('kkn.posko.update');
        Route::delete('/kkn/posko/{posko}', [AdminKknController::class, 'poskoDestroy'])->name('kkn.posko.destroy');
        Route::post('/kkn/posko/{posko}/assign', [AdminKknController::class, 'assignStudent'])->name('kkn.posko.assign');
        Route::delete('/kkn/pengajuan/{kkn}/remove', [AdminKknController::class, 'removeStudent'])->name('kkn.pengajuan.remove');

        Route::get('/laporan', [AdminPengajuanLaporanController::class, 'index'])->name('laporan.index');
        Route::delete('/laporan/bulk-delete', [AdminPengajuanLaporanController::class, 'bulkDestroy'])->name('laporan.bulk-delete');
        Route::get('/laporan/{laporan}', [AdminPengajuanLaporanController::class, 'show'])->name('laporan.show');
        Route::post('/laporan/{laporan}/pesan', [AdminPengajuanLaporanController::class, 'storeMessage'])->name('laporan.pesan.store');

        Route::get('/publikasi', [\App\Http\Controllers\PublikasiKkController::class, 'index'])->name('publikasi.index');
        Route::get('/publikasi/export-excel', [\App\Http\Controllers\PublikasiKkController::class, 'exportExcel'])->name('publikasi.export-excel');
        Route::get('/publikasi/create', [\App\Http\Controllers\PublikasiKkController::class, 'create'])->name('publikasi.create');
        Route::post('/publikasi', [\App\Http\Controllers\PublikasiKkController::class, 'store'])->name('publikasi.store');
        Route::get('/publikasi/{publikasiKk}/edit', [\App\Http\Controllers\PublikasiKkController::class, 'edit'])->name('publikasi.edit');
        Route::put('/publikasi/{publikasiKk}', [\App\Http\Controllers\PublikasiKkController::class, 'update'])->name('publikasi.update');
        Route::delete('/publikasi/{publikasiKk}', [\App\Http\Controllers\PublikasiKkController::class, 'destroy'])->name('publikasi.destroy');
        Route::get('/publikasi/{publikasiKk}/download', [\App\Http\Controllers\PublikasiKkController::class, 'download'])->name('publikasi.download');
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
        Route::get('/krs/{krs}/pdf', [MahasiswaKrsController::class, 'downloadPdf'])->name('krs.pdf');

        Route::get('/cuti', [MahasiswaCutiController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/create', [MahasiswaCutiController::class, 'create'])->name('cuti.create');
        Route::post('/cuti', [MahasiswaCutiController::class, 'store'])->name('cuti.store');
        Route::get('/cuti/{cuti}', [MahasiswaCutiController::class, 'show'])->name('cuti.show');
        Route::delete('/cuti/{cuti}', [MahasiswaCutiController::class, 'destroy'])->name('cuti.destroy');
        Route::get('/cuti/{cuti}/pdf', [MahasiswaCutiController::class, 'downloadPdf'])->name('cuti.pdf');

        Route::get('/khs', [MahasiswaKhsController::class, 'index'])->name('khs.index');
        Route::get('/khs/{khs}', [MahasiswaKhsController::class, 'show'])->name('khs.show');
        Route::get('/khs/{khs}/pdf', [MahasiswaKhsController::class, 'pdf'])->name('khs.pdf');

        Route::get('/absensi', [MahasiswaAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/materi/{absensi}', [MahasiswaAbsensiController::class, 'materi'])->name('absensi.materi');
        Route::get('/absensi/{mataKuliah}/{semester}', [MahasiswaAbsensiController::class, 'show'])->name('absensi.show');
        Route::get('/absensi/{mataKuliah}/{semester}/pdf', [MahasiswaAbsensiController::class, 'pdf'])->name('absensi.pdf');

        Route::get('/pembayaran', [MahasiswaDashboardController::class, 'pembayaran'])->name('pembayaran.index');
        Route::post('/pembayaran/{pembayaran}/upload', [MahasiswaDashboardController::class, 'uploadPembayaran'])->name('pembayaran.upload');

        Route::get('/kalender-akademik', [MahasiswaAcademicCalendarController::class, 'index'])->name('kalender.index');
        Route::get('/kalender-akademik/pdf', [MahasiswaAcademicCalendarController::class, 'pdf'])->name('kalender.pdf');

        Route::get('/skripsi', [MahasiswaSkripsiController::class, 'index'])->name('skripsi.index');
        Route::get('/skripsi/create', [MahasiswaSkripsiController::class, 'create'])->name('skripsi.create');
        Route::post('/skripsi', [MahasiswaSkripsiController::class, 'store'])->name('skripsi.store');
        Route::get('/skripsi/{skripsi}', [MahasiswaSkripsiController::class, 'show'])->name('skripsi.show');
        Route::get('/skripsi/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'downloadSkPembimbing'])->name('skripsi.sk.download');
        Route::get('/skripsi/{skripsi}/sk-pembimbing/preview', [AdminSkripsiController::class, 'previewSkPembimbing'])->name('skripsi.sk.preview');
        Route::get('/skripsi/{skripsi}/bimbingan', [MahasiswaSkripsiBimbinganController::class, 'show'])->name('skripsi.bimbingan');
        Route::get('/skripsi/{skripsi}/bimbingan/pdf', [MahasiswaSkripsiBimbinganController::class, 'pdf'])->name('skripsi.bimbingan.pdf');
        Route::post('/skripsi/{skripsi}/bimbingan', [MahasiswaSkripsiBimbinganController::class, 'store'])->name('skripsi.bimbingan.store');
        Route::get('/skripsi/{skripsi}/revisi', [MahasiswaSkripsiRevisiController::class, 'index'])->name('skripsi.revisi');
        Route::get('/skripsi/{skripsi}/revisi/pdf', [MahasiswaSkripsiRevisiController::class, 'pdf'])->name('skripsi.revisi.pdf');
        Route::get('/skripsi-files', [MahasiswaSkripsiFileController::class, 'index'])->name('skripsi-files.index');
        Route::post('/skripsi-files', [MahasiswaSkripsiFileController::class, 'store'])->name('skripsi-files.store');
        Route::get('/skripsi-files/{file}/preview', [MahasiswaSkripsiFileController::class, 'preview'])->name('skripsi-files.preview');
        Route::get('/skripsi-files/{file}/download', [MahasiswaSkripsiFileController::class, 'download'])->name('skripsi-files.download');
        Route::delete('/skripsi-files/{file}', [MahasiswaSkripsiFileController::class, 'destroy'])->name('skripsi-files.destroy');

        Route::get('/ppl', [MahasiswaPplController::class, 'index'])->name('ppl.index');
        Route::get('/ppl/create', [MahasiswaPplController::class, 'create'])->name('ppl.create');
        Route::post('/ppl', [MahasiswaPplController::class, 'store'])->name('ppl.store');
        Route::get('/ppl/{ppl}', [MahasiswaPplController::class, 'show'])->name('ppl.show');
        Route::get('/ppl/{ppl}/sk-pembimbing', [AdminPplController::class, 'downloadSkPembimbing'])->name('ppl.sk.download');
        Route::get('/ppl/{ppl}/sk-pembimbing/preview', [AdminPplController::class, 'previewSkPembimbing'])->name('ppl.sk.preview');
        Route::get('/ppl/{ppl}/bimbingan', [MahasiswaPplBimbinganController::class, 'show'])->name('ppl.bimbingan');
        Route::get('/ppl/{ppl}/bimbingan/pdf', [MahasiswaPplBimbinganController::class, 'pdf'])->name('ppl.bimbingan.pdf');
        Route::post('/ppl/{ppl}/bimbingan', [MahasiswaPplBimbinganController::class, 'store'])->name('ppl.bimbingan.store');
        Route::get('/ppl/{ppl}/revisi', [MahasiswaPplRevisiController::class, 'index'])->name('ppl.revisi');
        Route::get('/ppl/{ppl}/revisi/pdf', [MahasiswaPplRevisiController::class, 'pdf'])->name('ppl.revisi.pdf');
        Route::get('/ppl-files', [MahasiswaPplFileController::class, 'index'])->name('ppl-files.index');
        Route::post('/ppl-files', [MahasiswaPplFileController::class, 'store'])->name('ppl-files.store');
        Route::get('/ppl-files/{file}/preview', [MahasiswaPplFileController::class, 'preview'])->name('ppl-files.preview');
        Route::get('/ppl-files/{file}/download', [MahasiswaPplFileController::class, 'download'])->name('ppl-files.download');
        Route::delete('/ppl-files/{file}', [MahasiswaPplFileController::class, 'destroy'])->name('ppl-files.destroy');

        // KKN
        Route::get('/kkn', [MahasiswaKknController::class, 'index'])->name('kkn.index');
        Route::post('/kkn', [MahasiswaKknController::class, 'store'])->name('kkn.store');
        Route::get('/kkn/posko/{posko}', [MahasiswaKknController::class, 'showPosko'])->name('kkn.posko');

        Route::get('/laporan', [MahasiswaPengajuanLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/create', [MahasiswaPengajuanLaporanController::class, 'create'])->name('laporan.create');
        Route::post('/laporan', [MahasiswaPengajuanLaporanController::class, 'store'])->name('laporan.store');
        Route::get('/laporan/{laporan}', [MahasiswaPengajuanLaporanController::class, 'show'])->name('laporan.show');
        Route::post('/laporan/{laporan}/pesan', [MahasiswaPengajuanLaporanController::class, 'storeMessage'])->name('laporan.pesan.store');

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
        Route::get('/absensi/manual', [AdminAbsensiController::class, 'downloadManual'])->name('absensi.manual');
        Route::get('/absensi/rekap', [AdminAbsensiController::class, 'exportRekapPdf'])->name('absensi.rekap');
        Route::get('/absensi/entry', [AdminAbsensiController::class, 'entry'])->name('absensi.entry');
        Route::post('/absensi/{absensi}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::get('/absensi/{absensi}/export/pdf', [AdminAbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        Route::get('/absensi/{absensi}/materi', [AdminAbsensiController::class, 'materiFile'])->name('absensi.materi');
        Route::delete('/absensi/{absensi}/materi', [AdminAbsensiController::class, 'destroyMateriFile'])->name('absensi.materi.destroy');
        Route::get('/absensi/{absensi}/export/excel', [AdminAbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
        Route::get('/absensi/rekap/pdf', [AdminAbsensiController::class, 'exportRekapPdf'])->name('absensi.rekap');

        Route::get('/krs/approval', [DosenKrsApprovalController::class, 'index'])->name('krs.approval');
        Route::get('/krs/{krs}', [DosenKrsApprovalController::class, 'show'])->name('krs.show');
        Route::patch('/krs/{krs}', [DosenKrsApprovalController::class, 'updateStatus'])->name('krs.update');

        Route::get('/cuti', [DosenCutiApprovalController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/{cuti}', [DosenCutiApprovalController::class, 'show'])->name('cuti.show');
        Route::patch('/cuti/{cuti}/status', [DosenCutiApprovalController::class, 'updateStatus'])->name('cuti.status');

        Route::get('/profil', [DosenProfilController::class, 'show'])->name('profil');
        Route::post('/profil', [DosenProfilController::class, 'update'])->name('profil.update');
        Route::get('/profil/pdf', [DosenProfilController::class, 'pdf'])->name('profil.pdf');

        Route::get('/kalender-akademik', [DosenAcademicCalendarController::class, 'index'])->name('kalender.index');
        Route::get('/kalender-akademik/pdf', [DosenAcademicCalendarController::class, 'pdf'])->name('kalender.pdf');

        Route::get('/skripsi/bimbingan', [DosenSkripsiBimbinganController::class, 'index'])->name('skripsi.bimbingan.index');
        Route::get('/skripsi/{skripsi}/bimbingan', [DosenSkripsiBimbinganController::class, 'show'])->name('skripsi.bimbingan.show');
        Route::get('/skripsi/{skripsi}/bimbingan/pdf', [DosenSkripsiBimbinganController::class, 'pdf'])->name('skripsi.bimbingan.pdf');
        Route::post('/skripsi/{skripsi}/bimbingan', [DosenSkripsiBimbinganController::class, 'store'])->name('skripsi.bimbingan.store');
        Route::post('/skripsi/{skripsi}/bimbingan/file', [DosenSkripsiBimbinganController::class, 'storeFile'])->name('skripsi.bimbingan.file.store');
        Route::get('/skripsi/{skripsi}/revisi', [DosenSkripsiRevisiController::class, 'index'])->name('skripsi.revisi');
        Route::post('/skripsi/{skripsi}/revisi', [DosenSkripsiRevisiController::class, 'store'])->name('skripsi.revisi.store');
        Route::get('/skripsi/{skripsi}/revisi/pdf', [DosenSkripsiRevisiController::class, 'pdf'])->name('skripsi.revisi.pdf');

        Route::get('/skripsi/pengajuan', [AdminSkripsiController::class, 'index'])->name('skripsi-pengajuan.index');
        Route::get('/skripsi/pengajuan/{skripsi}', [AdminSkripsiController::class, 'show'])->name('skripsi.show');
        Route::get('/skripsi/pengajuan/{skripsi}/pdf', [AdminSkripsiController::class, 'downloadPdf'])->name('skripsi.pdf');
        Route::patch('/skripsi/pengajuan/{skripsi}/status', [AdminSkripsiController::class, 'updateStatus'])->name('skripsi-pengajuan.status');
        Route::patch('/skripsi/pengajuan/{skripsi}/assign', [AdminSkripsiController::class, 'assign'])->name('skripsi-pengajuan.assign');
        Route::delete('/skripsi/pengajuan/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'destroySkPembimbing'])->name('skripsi-pengajuan.sk.destroy');
        Route::delete('/skripsi/pengajuan/{skripsi}/pembimbing', [AdminSkripsiController::class, 'resetPembimbing'])->name('skripsi-pengajuan.pembimbing.reset');
        Route::get('/skripsi/pengajuan/{skripsi}/sk-pembimbing', [AdminSkripsiController::class, 'downloadSkPembimbing'])->name('skripsi-pengajuan.sk.download');
        Route::get('/skripsi/pengajuan/{skripsi}/sk-pembimbing/preview', [AdminSkripsiController::class, 'previewSkPembimbing'])->name('skripsi-pengajuan.sk.preview');

        Route::get('/ppl/bimbingan', [DosenPplBimbinganController::class, 'index'])->name('ppl.bimbingan.index');
        Route::get('/ppl/{ppl}/bimbingan', [DosenPplBimbinganController::class, 'show'])->name('ppl.bimbingan.show');
        Route::post('/ppl/{ppl}/bimbingan', [DosenPplBimbinganController::class, 'store'])->name('ppl.bimbingan.store');
        Route::post('/ppl/{ppl}/bimbingan/file', [DosenPplBimbinganController::class, 'storeFile'])->name('ppl.bimbingan.file.store');
        Route::get('/ppl/{ppl}/bimbingan/pdf', [DosenPplBimbinganController::class, 'pdf'])->name('ppl.bimbingan.pdf');
        Route::get('/ppl/{ppl}/revisi', [DosenPplRevisiController::class, 'index'])->name('ppl.revisi');
        Route::post('/ppl/{ppl}/revisi', [DosenPplRevisiController::class, 'store'])->name('ppl.revisi.store');
        Route::get('/ppl/{ppl}/revisi/pdf', [DosenPplRevisiController::class, 'pdf'])->name('ppl.revisi.pdf');

        Route::get('/ppl/pengajuan', [AdminPplController::class, 'index'])->name('ppl-pengajuan.index');
        Route::get('/ppl/pengajuan/{ppl}', [AdminPplController::class, 'show'])->name('ppl.show');
        Route::get('/ppl/pengajuan/{ppl}/pdf', [AdminPplController::class, 'downloadPdf'])->name('ppl.pdf');
        Route::patch('/ppl/pengajuan/{ppl}/status', [AdminPplController::class, 'updateStatus'])->name('ppl-pengajuan.status');
        Route::patch('/ppl/pengajuan/{ppl}/assign', [AdminPplController::class, 'assign'])->name('ppl-pengajuan.assign');
        Route::get('/ppl/pengajuan/{ppl}/sk-pembimbing', [AdminPplController::class, 'downloadSkPembimbing'])->name('ppl-pengajuan.sk.download');
        Route::get('/ppl/pengajuan/{ppl}/sk-pembimbing/preview', [AdminPplController::class, 'previewSkPembimbing'])->name('ppl-pengajuan.sk.preview');

        // KKN
        Route::get('/kkn', [DosenKknController::class, 'index'])->name('kkn.index');
        Route::get('/kkn/posko/{posko}', [DosenKknController::class, 'showPosko'])->name('kkn.posko');

        Route::get('/laporan', [DosenPengajuanLaporanController::class, 'index'])->name('laporan.index');
        Route::delete('/laporan/bulk-delete', [DosenPengajuanLaporanController::class, 'bulkDestroy'])->name('laporan.bulk-delete');
        Route::get('/laporan/{laporan}', [DosenPengajuanLaporanController::class, 'show'])->name('laporan.show');
        Route::post('/laporan/{laporan}/pesan', [DosenPengajuanLaporanController::class, 'storeMessage'])->name('laporan.pesan.store');

        Route::get('/publikasi', [\App\Http\Controllers\PublikasiKkController::class, 'index'])->name('publikasi.index');
        Route::get('/publikasi/export-excel', [\App\Http\Controllers\PublikasiKkController::class, 'exportExcel'])->name('publikasi.export-excel');
        Route::get('/publikasi/create', [\App\Http\Controllers\PublikasiKkController::class, 'create'])->name('publikasi.create');
        Route::post('/publikasi', [\App\Http\Controllers\PublikasiKkController::class, 'store'])->name('publikasi.store');
        Route::get('/publikasi/{publikasiKk}/edit', [\App\Http\Controllers\PublikasiKkController::class, 'edit'])->name('publikasi.edit');
        Route::put('/publikasi/{publikasiKk}', [\App\Http\Controllers\PublikasiKkController::class, 'update'])->name('publikasi.update');
        Route::delete('/publikasi/{publikasiKk}', [\App\Http\Controllers\PublikasiKkController::class, 'destroy'])->name('publikasi.destroy');
        Route::get('/publikasi/{publikasiKk}/download', [\App\Http\Controllers\PublikasiKkController::class, 'download'])->name('publikasi.download');
    });

Route::prefix('keuangan')
    ->name('keuangan.')
    ->middleware(['auth', 'role:keuangan,admin'])
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('pembayaran/export/pdf', [KeuanganPembayaranController::class, 'exportPdf'])->name('pembayaran.export.pdf');
        Route::get('pembayaran/export/excel', [KeuanganPembayaranController::class, 'exportExcel'])->name('pembayaran.export.excel');
        Route::get('pembayaran/{pembayaran}/pdf', [KeuanganPembayaranController::class, 'downloadPdf'])->name('pembayaran.pdf');
        Route::patch('pembayaran/{pembayaran}/detail/{detail}/status', [KeuanganPembayaranController::class, 'updateDetailStatus'])->name('pembayaran.detail.status');
        Route::delete('pembayaran/bulk-delete', [KeuanganPembayaranController::class, 'bulkDestroy'])->name('pembayaran.bulk-delete');
        Route::resource('pembayaran', KeuanganPembayaranController::class);
        Route::post('pembayaran/{pembayaran}/cicilan', [KeuanganPembayaranController::class, 'addCicilan'])->name('pembayaran.cicilan');
    });

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin,dosen,mahasiswa'])->group(function () {
    Route::get('/files/skripsi/{file}/preview', [AdminSkripsiController::class, 'previewFile'])->name('files.skripsi.preview');
    Route::get('/files/skripsi/{file}/download', [AdminSkripsiController::class, 'downloadFile'])->name('files.skripsi.download');
    Route::get('/files/ppl/{file}/preview', [AdminPplController::class, 'previewFile'])->name('files.ppl.preview');
    Route::get('/files/ppl/{file}/download', [AdminPplController::class, 'downloadFile'])->name('files.ppl.download');

    // Shared KKN Bimbingan
    Route::get('/files/kkn/{file}/preview', [KknBimbinganController::class, 'previewFile'])->name('files.kkn.preview');
    Route::get('/files/kkn/{file}/download', [KknBimbinganController::class, 'downloadFile'])->name('files.kkn.download');
    Route::post('/kkn/posko/{posko}/message', [KknBimbinganController::class, 'sendMessage'])->name('kkn.bimbingan.message');
    Route::post('/kkn/posko/{posko}/file', [KknBimbinganController::class, 'uploadFile'])->name('kkn.bimbingan.file');
    Route::delete('/kkn/file/{file}', [KknBimbinganController::class, 'deleteFile'])->name('kkn.bimbingan.file.destroy');
    Route::post('/kkn/posko/{posko}/revisi', [KknBimbinganController::class, 'storeRevisi'])->name('kkn.bimbingan.revisi.store');
    Route::delete('/kkn/revisi/{revisi}', [KknBimbinganController::class, 'destroyRevisi'])->name('kkn.bimbingan.revisi.destroy');
    Route::get('/kkn/posko/{posko}/revisi/print', [KknBimbinganController::class, 'printRevisi'])->name('kkn.bimbingan.revisi.print');
});
