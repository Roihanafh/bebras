<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\KoordinatorBiro;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KoordinatorBiroController extends Controller
{
    private function breadCrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Berita', 'url' => route('berita.index')],
            ['label' => 'Koordinator Biro', 'url' => route('biro_koordinator.index')],
            ['label' => $currentLabel],
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $totalKoordinator = $user->koordinatorBiro()->count();
        $breadcrumbs = $this->breadCrumbs('Daftar Koordinator');

        return view('biro.koordinator', compact('user', 'totalKoordinator', 'breadcrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $koordinators = $user->koordinatorBiro()->orderBy('nama', 'asc');

            return DataTables::of($koordinators)
                ->addIndexColumn()
                ->addColumn('email_display', function ($row) {
                    return '<a href="mailto:' . e($row->email) . '" class="text-primary">' . e($row->email) . '</a>';
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<button type="button" class="btn btn-sm btn-warning btn-edit-koordinator me-1" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" data-email="' . e($row->email) . '">';
                    $btn .= '<i class="bx bx-edit-alt me-1"></i>Edit';
                    $btn .= '</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete-koordinator" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '">';
                    $btn .= '<i class="bx bx-trash me-1"></i>Hapus';
                    $btn .= '</button>';
                    return $btn;
                })
                ->rawColumns(['email_display', 'aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $koordinator = auth()->user()->koordinatorBiro()->create([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil ditambahkan!',
            'data'    => $koordinator,
        ]);
    }

    public function edit($id)
    {
        // Hanya mencari koordinator yang berelasi dengan user biro yang sedang login
        $koordinator = KoordinatorBiro::where('user_id', auth()->id())->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $koordinator,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Hanya mengupdate koordinator yang berelasi dengan user biro yang sedang login
        $koordinator = KoordinatorBiro::where('user_id', auth()->id())->findOrFail($id);

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

    public function destroy($id)
    {
        // Hanya menghapus koordinator yang berelasi dengan user biro yang sedang login
        $koordinator = KoordinatorBiro::where('user_id', auth()->id())->findOrFail($id);
        $koordinator->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil dihapus!',
        ]);
    }
}
