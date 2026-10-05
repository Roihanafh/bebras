<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Kegiatan::where('tipe', 'berita')
            ->where('status_validasi', 'approved')
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.berita.index', compact('beritas'));
    }
}
