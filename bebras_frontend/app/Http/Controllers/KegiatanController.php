<?php

namespace App\Http\Controllers;

use App\Models\MenuKegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Dynamic page: /kegiatan/{slug}
     * Loads the MenuKegiatan entry by slug and its related kegiatan cards.
     */
    public function show(string $slug)
    {
        $menu = MenuKegiatan::with(['kegiatans' => function ($query) {
                $query->where('status_validasi', 'approved');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        // If this menu has an external URL, redirect to it
        if ($menu->url) {
            return redirect()->away($menu->url);
        }

        return view('pages.kegiatan.show', compact('menu'));
    }
}
