<?php

namespace App\Http\Controllers;

use App\Models\AnggotaBiro;
use App\Models\TentangBebras;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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
                    return $u->koordinatorBiro;
                })->filter(function ($k) use ($primary) {
                    // Hindari memasukkan nama institusi biro itu sendiri sebagai nama koordinator
                    return !empty($k->nama) && strtolower(trim($k->nama)) !== strtolower(trim($primary->name));
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

    /**
     * Tampilkan halaman kartu member anggota biro (terverifikasi)
     */
    public function memberCard(Request $request, $id)
    {
        $anggota = AnggotaBiro::with('biro')
            ->where('status_validasi', 'approved')
            ->findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'                  => $anggota->id,
                    'nama'                => $anggota->nama,
                    'id_member'           => $anggota->formatted_id_member,
                    'biro_nama'           => $anggota->nama_biro,
                    'expiry_date'         => $anggota->formatted_expiry_date,
                    'qr_svg'              => $anggota->qr_code_svg,
                    'download_url'        => route('tentangBebras.anggota.card.download', $anggota->id),
                    'view_url'            => route('tentangBebras.anggota.card', $anggota->id),
                ],
            ]);
        }

        return view('pages.tentangBebras.member_card', compact('anggota'));
    }

    /**
     * Download kartu member dalam format PDF (A4 / custom card size landscape)
     */
    public function downloadMemberCard($id)
    {
        $anggota = AnggotaBiro::with('biro')
            ->where('status_validasi', 'approved')
            ->findOrFail($id);

        $templatePath = public_path('img/membership_card_template.png');
        if (!file_exists($templatePath)) {
            abort(500, 'Template kartu member tidak ditemukan.');
        }

        $templateBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($templatePath));
        $qrBase64 = $anggota->qr_code_base64;

        $pdf = Pdf::loadView('pages.tentangBebras.member_card_pdf', compact('anggota', 'templateBase64', 'qrBase64'))
            ->setPaper([0, 0, 1016, 638], 'landscape');

        $filename = 'kartu-member-' . $anggota->formatted_id_member . '.pdf';
        return $pdf->download($filename);
    }
}

