<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MenuKegiatanController;
use App\Http\Controllers\BerandaCmsController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LatihanController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SoalBookController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\TentangBebrasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Berita\BeritaBiroController;
use App\Http\Controllers\Berita\BeritaAdminController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('app');
// });

Route::get('/', [AuthController::class, 'showLogin']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::prefix('akun')->group(function () {
        Route::get('/register', [RegisterController::class, 'index'])->name('register');
        Route::get('/get-akun', [RegisterController::class, 'getAkun'])->name('register.get-akun');
        Route::post('/store', [RegisterController::class, 'store'])->name('store.akun');
        Route::get('/{user}/edit', [RegisterController::class, 'edit'])->name('edit.akun');
        Route::put('/{user}', [RegisterController::class, 'update'])->name('update.akun');
        Route::delete('/{user}', [RegisterController::class, 'destroy'])->name('destroy.akun');
    });

    Route::prefix('tentang-bebras')->group(function () {
        Route::get('/', [TentangBebrasController::class, 'index'])->name('tentang_bebras.index');
        Route::get('/form', [TentangBebrasController::class, 'create'])->name('form-tentang-bebras');
        Route::post('/store', [TentangBebrasController::class, 'store'])->name('tentang_bebras.store');
        Route::get('/get-data', [TentangBebrasController::class, 'getData'])->name('tentang_bebras.data');
        Route::get('/{id}', [TentangBebrasController::class, 'show'])->name('tentang_bebras.show');
        Route::get('/{id}/edit', [TentangBebrasController::class, 'edit'])->name('tentang_bebras.edit');
        Route::put('/{id}', [TentangBebrasController::class, 'update'])->name('tentang_bebras.update');
        Route::delete('/{id}', [TentangBebrasController::class, 'destroy'])->name('tentang_bebras.destroy');

        // Child items routes
        Route::post('/{tentang_bebras_id}/items', [TentangBebrasController::class, 'storeItemChild'])->name('tentang_bebras.items.store');
        Route::put('/items/{id}', [TentangBebrasController::class, 'updateItemChild'])->name('tentang_bebras.items.update');
        Route::delete('/items/{id}', [TentangBebrasController::class, 'destroyItemChild'])->name('tentang_bebras.items.destroy');
    });

    Route::prefix('soal-bebras')->group(function () {
        Route::get('/', [SoalController::class, 'index'])->name('soal_bebras.index');
        Route::get('/form', [SoalController::class, 'create'])->name('form-soal-bebras');
        Route::post('/store', [SoalController::class, 'store'])->name('soal_bebras.store');
        Route::get('/{id}', [SoalController::class, 'show'])->name('soal_bebras.show');
        Route::get('/{id}/edit', [SoalController::class, 'edit'])->name('soal_bebras.edit');
        Route::put('/{id}', [SoalController::class, 'update'])->name('soal_bebras.update');
        Route::delete('/{id}', [SoalController::class, 'destroy'])->name('soal_bebras.destroy');

        // Menu Soal Items (konsep & kriteria)
        Route::get('/{id}/items', [SoalController::class, 'getItems'])->name('soal_bebras.items.get');
        Route::post('/{id}/items', [SoalController::class, 'storeItem'])->name('soal_bebras.items.store');
        Route::put('/items/{itemId}', [SoalController::class, 'updateItem'])->name('soal_bebras.items.update');
        Route::delete('/items/{itemId}', [SoalController::class, 'destroyItem'])->name('soal_bebras.items.destroy');

        // Soal Challenge (nested)
        Route::get('/{id}/challenge/create', [SoalController::class, 'createChallenge'])->name('soal_bebras.challenge.create');
        Route::post('/{id}/challenge', [SoalController::class, 'storeChallenge'])->name('soal_bebras.challenge.store');
        Route::get('/challenge/{cid}/edit', [SoalController::class, 'editChallenge'])->name('soal_bebras.challenge.edit');
        Route::put('/challenge/{cid}', [SoalController::class, 'updateChallenge'])->name('soal_bebras.challenge.update');
        Route::delete('/challenge/{cid}', [SoalController::class, 'destroyChallenge'])->name('soal_bebras.challenge.destroy');

        // Challenge Options (A/B/C/D)
        Route::post('/challenge/{cid}/options', [SoalController::class, 'storeOption'])->name('soal_bebras.option.store');
        Route::put('/options/{oid}', [SoalController::class, 'updateOption'])->name('soal_bebras.option.update');
        Route::delete('/options/{oid}', [SoalController::class, 'destroyOption'])->name('soal_bebras.option.destroy');
    });

    // Sumber Buku Pembahasan Soal
    Route::prefix('soal-book')->group(function () {
        Route::get('/', [SoalBookController::class, 'index'])->name('soal_book.index');
        Route::get('/create', [SoalBookController::class, 'create'])->name('soal_book.create');
        Route::post('/store', [SoalBookController::class, 'store'])->name('soal_book.store');
        Route::get('/{id}/edit', [SoalBookController::class, 'edit'])->name('soal_book.edit');
        Route::put('/{id}', [SoalBookController::class, 'update'])->name('soal_book.update');
        Route::delete('/{id}', [SoalBookController::class, 'destroy'])->name('soal_book.destroy');
    });

    Route::prefix('kontak')->group(function () {
        Route::get('/', [KontakController::class, 'index'])->name('kontak.index');
        Route::post('/store', [KontakController::class, 'store'])->name('kontak.store');
        Route::get('/list', [KontakController::class, 'list'])->name('kontak.list');
        Route::get('/detail/{id}', [KontakController::class, 'detail'])->name('kontak.detail');
        Route::get('/{id}/edit', [KontakController::class, 'edit'])->name('kontak.edit');
        Route::put('/{id}', [KontakController::class, 'update'])->name('kontak.update');
        Route::delete('/{id}', [KontakController::class, 'destroy'])->name('kontak.destroy');
    });

    Route::prefix('latihan')->group(function () {
        Route::get('/', [LatihanController::class, 'index'])->name('latihan.index');
        Route::post('/store', [LatihanController::class, 'store'])->name('latihan.store');
        Route::get('/list', [LatihanController::class, 'list'])->name('latihan.list');
        Route::get('/{id}/edit', [LatihanController::class, 'edit'])->name('latihan.edit');
        Route::post('/{id}/update', [LatihanController::class, 'update'])->name('latihan.update');
        Route::delete('/{id}', [LatihanController::class, 'destroy'])->name('latihan.destroy');
        Route::get('/{id}/deskripsi', [LatihanController::class, 'deskripsi'])->name('latihan.deskripsi');
    });

    // Banner (Carousel)
    Route::prefix('banner')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('banner.index');
        Route::get('/list', [BannerController::class, 'list'])->name('banner.list');
        Route::get('/create', [BannerController::class, 'create'])->name('banner.create');
        Route::post('/store', [BannerController::class, 'store'])->name('banner.store');
        Route::get('/{id}/edit', [BannerController::class, 'edit'])->name('banner.edit');
        Route::put('/{id}', [BannerController::class, 'update'])->name('banner.update');
        Route::delete('/{id}', [BannerController::class, 'destroy'])->name('banner.destroy');
    });

    // Menu Kegiatan (Hierarki Navbar)
    Route::prefix('menu-kegiatan')->group(function () {
        Route::get('/', [MenuKegiatanController::class, 'index'])->name('menu_kegiatan.index');
        Route::get('/create', [MenuKegiatanController::class, 'create'])->name('menu_kegiatan.create');
        Route::post('/store', [MenuKegiatanController::class, 'store'])->name('menu_kegiatan.store');
        Route::get('/{id}/edit', [MenuKegiatanController::class, 'edit'])->name('menu_kegiatan.edit');
        Route::put('/{id}', [MenuKegiatanController::class, 'update'])->name('menu_kegiatan.update');
        Route::delete('/{id}', [MenuKegiatanController::class, 'destroy'])->name('menu_kegiatan.destroy');
    });

    // Kegiatan (item kartu per menu)
    Route::prefix('kegiatan')->group(function () {
        Route::get('/', [KegiatanController::class, 'index'])->name('kegiatan.index');
        Route::get('/list', [KegiatanController::class, 'list'])->name('kegiatan.list');
        Route::get('/create', [KegiatanController::class, 'create'])->name('kegiatan.create');
        Route::post('/store', [KegiatanController::class, 'store'])->name('kegiatan.store');
        Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('kegiatan.edit');
        Route::put('/{id}', [KegiatanController::class, 'update'])->name('kegiatan.update');
        Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    });

    // Pengaturan Situs
    Route::prefix('pengaturan')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/list', [SettingController::class, 'list'])->name('setting.list');
        Route::get('/create', [SettingController::class, 'create'])->name('setting.create');
        Route::post('/store', [SettingController::class, 'store'])->name('setting.store');
        Route::get('/{id}/edit', [SettingController::class, 'edit'])->name('setting.edit');
        Route::put('/{id}', [SettingController::class, 'update'])->name('setting.update');
        Route::delete('/{id}', [SettingController::class, 'destroy'])->name('setting.destroy');
    });

    // Manajemen Berita (Admin)
    Route::prefix('admin/berita')->name('admin.berita.')->group(function () {
        Route::get('/', [BeritaAdminController::class, 'index'])->name('index');
        Route::get('/list', [BeritaAdminController::class, 'list'])->name('list');
        Route::get('/{id}/edit', [BeritaAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BeritaAdminController::class, 'update'])->name('update');
        Route::post('/{id}/approve', [BeritaAdminController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [BeritaAdminController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [BeritaAdminController::class, 'destroy'])->name('destroy');
    });

    // CMS Beranda
    Route::prefix('beranda')->name('beranda.')->group(function () {
        Route::get('/', [BerandaCmsController::class, 'index'])->name('index');
        Route::put('/about', [BerandaCmsController::class, 'updateAbout'])->name('about.update');
        Route::put('/cta', [BerandaCmsController::class, 'updateCta'])->name('cta.update');

        // Kegiatan Beranda
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            Route::get('/list', [BerandaCmsController::class, 'list'])->name('list');
            Route::get('/create', [BerandaCmsController::class, 'kegiatanCreate'])->name('create');
            Route::post('/store', [BerandaCmsController::class, 'kegiatanStore'])->name('store');
            Route::get('/{id}/edit', [BerandaCmsController::class, 'kegiatanEdit'])->name('edit');
            Route::put('/{id}', [BerandaCmsController::class, 'kegiatanUpdate'])->name('update');
            Route::delete('/{id}', [BerandaCmsController::class, 'kegiatanDestroy'])->name('destroy');
        });
    });

});

// Routes untuk role biro — dilindungi middleware auth + role:biro
Route::middleware(['auth', 'role:biro'])->group(function () {
    Route::get('/berita', [BeritaBiroController::class, 'index'])->name('berita.index');
    Route::get('/berita/list', [BeritaBiroController::class, 'list'])->name('berita.list');
    Route::get('/berita/create', [BeritaBiroController::class, 'create'])->name('berita.create');
    Route::post('/berita/store', [BeritaBiroController::class, 'store'])->name('berita.store');
    Route::get('/berita/{id}/edit', [BeritaBiroController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{id}', [BeritaBiroController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{id}', [BeritaBiroController::class, 'destroy'])->name('berita.destroy');
});
