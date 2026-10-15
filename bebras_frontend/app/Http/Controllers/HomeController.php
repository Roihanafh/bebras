<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Setting;
use App\Models\Kegiatan;
use App\Models\Mitra;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('urutan', 'asc')->get();
        $kegiatans = Kegiatan::where('tipe', 'kegiatan_utama')->orderBy('urutan', 'asc')->get();
        $beritas = Kegiatan::with('gambarBerita')
            ->where('tipe', 'berita')
            ->where('status_validasi', 'approved')
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $pengumumans = Kegiatan::with('gambarBerita')
            ->where('tipe', 'pengumuman')
            ->where('status_validasi', 'approved')
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $popupPengumuman = Kegiatan::with('gambarBerita')
            ->where('tipe', 'pengumuman')
            ->where('status_validasi', 'approved')
            ->where('is_popup', true)
            ->latest('created_at')
            ->first();
        $aboutLogo = Setting::getByKey('home_about_logo', 'img/logo.jpg');
        $aboutContent = Setting::getByKey('home_about_content');
        $ctaTitle = Setting::getByKey('home_cta_title', 'Bebras Indonesia Challenge 2024');
        $ctaDescription = Setting::getByKey('home_cta_description');
        $ctaLink = Setting::getByKey('home_cta_link', '#');
        $mitras = Mitra::orderBy('urutan', 'asc')->get();

        return view('pages.home', compact(
            'banners',
            'kegiatans',
            'beritas',
            'pengumumans',
            'popupPengumuman',
            'aboutLogo',
            'aboutContent',
            'ctaTitle',
            'ctaDescription',
            'ctaLink',
            'mitras'
        ));
    }
}
