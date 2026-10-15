<?php

namespace App\Http\Controllers;

use App\Models\KoordinatorBiro;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BiroController extends Controller
{
    private function breadcrumbs(): array
    {
        return [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Biro'],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadcrumbs();
        return view('biro.index', compact('breadcrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'biro');
            })->withCount('koordinatorBiro')->with('koordinatorBiro');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('kode_biro_display', function ($row) {
                    if ($row->kode_biro) {
                        return '<span class="badge bg-label-primary fs-7">' . e($row->kode_biro) . '</span>';
                    }
                    return '<span class="text-muted fst-italic">-</span>';
                })
                ->addColumn('jumlah_koordinator', function ($row) {
                    return '<span class="badge bg-label-info">' . $row->koordinator_biro_count . ' Koordinator</span>';
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<button type="button" class="btn btn-sm btn-primary btn-manage-koordinator me-1" data-id="' . $row->id . '" data-name="' . e($row->name) . '" data-kode="' . e($row->kode_biro ?? '') . '">';
                    $btn .= '<i class="bx bx-group me-1"></i>Kelola Koordinator';
                    $btn .= '</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-kode" data-id="' . $row->id . '" data-name="' . e($row->name) . '" data-kode="' . e($row->kode_biro ?? '') . '">';
                    $btn .= '<i class="bx bx-hash me-1"></i>Edit Kode';
                    $btn .= '</button>';
                    return $btn;
                })
                ->rawColumns(['kode_biro_display', 'jumlah_koordinator', 'aksi'])
                ->make(true);
        }
    }

    public function updateKodeBiro(Request $request, $id)
    {
        $user = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->findOrFail($id);

        $request->validate([
            'kode_biro' => ['nullable', 'string', 'size:3'],
        ], [
            'kode_biro.size' => 'Kode biro harus terdiri dari 3 digit (contoh: 001).',
        ]);

        $user->kode_biro = $request->kode_biro;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kode biro berhasil diperbarui!',
            'data'    => $user,
        ]);
    }

    public function getKoordinator($id)
    {
        $user = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->with('koordinatorBiro')->findOrFail($id);

        return response()->json([
            'status'      => 'success',
            'biro'        => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'kode_biro' => $user->kode_biro,
            ],
            'koordinator' => $user->koordinatorBiro,
        ]);
    }

    public function storeKoordinator(Request $request, $id)
    {
        $user = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->findOrFail($id);

        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $koordinator = $user->koordinatorBiro()->create([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil ditambahkan!',
            'data'    => $koordinator,
        ]);
    }

    public function updateKoordinator(Request $request, $id)
    {
        $koordinator = KoordinatorBiro::findOrFail($id);

        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $koordinator->update([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil diperbarui!',
            'data'    => $koordinator,
        ]);
    }

    public function destroyKoordinator($id)
    {
        $koordinator = KoordinatorBiro::findOrFail($id);
        $koordinator->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil dihapus!',
        ]);
    }
}
