<?php

namespace App\Http\Controllers;

use App\Models\TentangBebras;
use App\Models\User;
use Illuminate\Http\Request;

class TentangBebrasController extends Controller
{
    public function show($slug)
    {
        $page = TentangBebras::with('items')->where('slug', $slug)->firstOrFail();
        // Use the stored template, fallback to dd_1 for old/unset rows
        $template = $page->template ?? 'dd_1';
        $viewName = 'pages.tentangBebras.' . $template;
        if (!view()->exists($viewName)) {
            $viewName = 'pages.tentangBebras.dd_1';
        }

        $biros = collect();
        if ($template === 'dd_7' || str_contains($slug, 'biro')) {
            $rawBiros = User::whereHas('roles', function ($q) {
                $q->where('name', 'biro');
            })->with([
                'koordinatorBiro',
                'anggotaBiro' => function ($q) {
                    $q->where('status_validasi', 'approved')
                        ->orderBy('no_anggota', 'asc')
                        ->orderBy('nama', 'asc');
                }
            ])->orderByRaw('kode_biro IS NULL, kode_biro ASC, name ASC')->get();

            // Kelompokkan akun user dengan biro yang sama agar biro tidak ditampilkan berkali-kali
            $grouped = $rawBiros->groupBy(function ($user) {
                if (!empty($user->kode_biro)) {
                    return 'kode_' . $user->kode_biro;
                }
                return 'name_' . trim(strtolower($user->name));
            });

            $biros = $grouped->map(function ($usersInBiro) {
                $primary = $usersInBiro->first();

                // Kumpulkan seluruh koordinator dari semua akun di biro yang sama
                $allKoordinator = $usersInBiro->flatMap(function ($u) {
                    if ($u->koordinatorBiro && $u->koordinatorBiro->isNotEmpty()) {
                        return $u->koordinatorBiro;
                    }
                    return collect([(object)[
                        'nama'  => $u->name,
                        'email' => $u->email,
                    ]]);
                })->unique(function ($k) {
                    return strtolower(trim($k->nama)) . '|' . strtolower(trim($k->email));
                })->values();

                // Kumpulkan seluruh anggota biro yang disetujui dari semua akun di biro yang sama
                $allAnggota = $usersInBiro->flatMap(function ($u) {
                    return $u->anggotaBiro;
                })->unique('id')->sortBy(function ($a) {
                    return ($a->no_anggota ?? '999') . '_' . $a->nama;
                })->values();

                // Kloning instance model dan pasang relasi yang telah digabung
                $biroObj = clone $primary;
                $biroObj->setRelation('koordinatorBiro', $allKoordinator);
                $biroObj->setRelation('anggotaBiro', $allAnggota);

                return $biroObj;
            })->values();
        }

        return view($viewName, compact('page', 'biros'));
    }
}
