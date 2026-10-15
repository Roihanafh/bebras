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
            $biros = User::whereHas('roles', function ($q) {
                $q->where('name', 'biro');
            })->with('koordinatorBiro')->orderByRaw('kode_biro IS NULL, kode_biro ASC, name ASC')->get();
        }

        return view($viewName, compact('page', 'biros'));
    }
}
