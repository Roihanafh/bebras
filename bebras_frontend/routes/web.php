<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
Route::get('/', [HomeController::class, 'index'])->name('home');

use App\Http\Controllers\TentangBebrasController;

Route::prefix('tentangBebras')->name('tentangBebras.')->group(function () {
    Route::get('/dd_1', [TentangBebrasController::class, 'show'])->defaults('slug', 'dd_1')->name('dd_1');
    Route::get('/dd_2', [TentangBebrasController::class, 'show'])->defaults('slug', 'dd_2')->name('dd_2');
    Route::get('/dd_3', [TentangBebrasController::class, 'show'])->defaults('slug', 'dd_3')->name('dd_3');
    Route::get('/dd_4', [TentangBebrasController::class, 'show'])->defaults('slug', 'dd_4')->name('dd_4');
    Route::get('/dd_5', [TentangBebrasController::class, 'show'])->defaults('slug', 'dd_5')->name('dd_5');
    Route::get('/dd_6', [TentangBebrasController::class, 'show'])->defaults('slug', 'dd_6')->name('dd_6');
    Route::get('/{slug}', [TentangBebrasController::class, 'show'])->name('show');
});

use App\Http\Controllers\SoalController;

Route::prefix('soal')->name('soal.')->group(function () {
    Route::get('/index-soal', [SoalController::class, 'indexSoal'])->name('index-soal');
    Route::get('/pembahasan-soal', [SoalController::class, 'pembahasanSoal'])->name('pembahasan-soal');
    Route::get('/siaga-sd', [SoalController::class, 'challenge'])->defaults('slug', 'siaga-sd')->name('siaga-sd');
    Route::get('/penggalang-smp', [SoalController::class, 'challenge'])->defaults('slug', 'penggalang-smp')->name('penggalang-smp');
    Route::get('/penegak-sma', [SoalController::class, 'challenge'])->defaults('slug', 'penegak-sma')->name('penegak-sma');
    Route::get('/{slug}', [SoalController::class, 'show'])->name('show');
});

use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\BeritaController;

Route::get('/kegiatan/berita', [BeritaController::class, 'index'])->name('berita');

Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
    Route::get('/{slug}', [KegiatanController::class, 'show'])->name('show');
});

Route::get('/latihan', function () {
    $platforms = \App\Models\Latihan::all();
    return view('pages.latihan.index', compact('platforms'));
})->name('latihan');

Route::get('/kontak', function () {
    $contacts = \App\Models\Kontak::with('details')->get();
    return view('pages.kontak.index', compact('contacts'));
})->name('kontak');


