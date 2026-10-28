<?php

namespace App\Http\Controllers;

use App\Models\MenuKegiatan;
use App\Models\Statistic;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Dynamic page: /kegiatan/{slug}
     * Loads the MenuKegiatan entry by slug and its related kegiatan cards.
     */
    public function show(string $slug)
    {
        $menu = MenuKegiatan::with(['parent', 'pengumumanHasilFiles', 'kegiatans' => function ($query) {
                $query->where('status_validasi', 'approved');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        // Menu dengan children bukan halaman konten — 404
        if ($menu->children()->exists()) {
            abort(404);
        }

        // Jika menu memiliki URL eksternal, redirect sebelum cek template
        if ($menu->url) {
            return redirect()->away($menu->url);
        }

        $template = $menu->resolveTemplate();

        if ($template === 'statistik') {
            $statistik = Statistic::orderBy('year')->get();
            return view('pages.kegiatan.statistik', compact('menu', 'statistik'));
        }

        return match($template) {
            'bebras_challenge' => view('pages.kegiatan.bebras_challenge', compact('menu')),
            'workshop'         => view('pages.kegiatan.workshop', compact('menu')),
            'pengumuman_hasil' => view('pages.kegiatan.pengumuman_hasil', compact('menu')),
            default            => view('pages.kegiatan.show', compact('menu')),
        };
    }
}
