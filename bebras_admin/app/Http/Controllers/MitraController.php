<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MitraController extends Controller
{
    private function breadCrumbs(): array
    {
        return [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Mitra'],
        ];
    }

    public function index()
    {
        $breadCrumbs = $this->breadCrumbs();
        return view('mitra.index', compact('breadCrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $mitra = Mitra::query();
            return DataTables::of($mitra)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    if ($row->logo) {
                        return '<img src="' . asset('storage/' . $row->logo) . '" alt="' . $row->nama . '" height="40">';
                    }
                    return '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '<button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">Edit</button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Hapus</button>';
                })
                ->rawColumns(['logo', 'aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $path = null;

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('mitra', 'public');
            }

            Mitra::create([
                'nama'   => $request->nama,
                'logo'   => $path,
                'urutan' => $request->urutan ?? 0,
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $mitra = Mitra::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'id'   => $mitra->id,
                    'nama' => $mitra->nama,
                    'logo' => $mitra->logo,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $mitra = Mitra::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = ['nama' => $request->nama];

            if ($request->hasFile('logo')) {
                if ($mitra->logo && Storage::disk('public')->exists($mitra->logo)) {
                    Storage::disk('public')->delete($mitra->logo);
                }
                $data['logo'] = $request->file('logo')->store('mitra', 'public');
            }

            if ($request->has('urutan')) {
                $data['urutan'] = $request->urutan;
            }

            $mitra->update($data);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $mitra = Mitra::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            if ($mitra->logo && Storage::disk('public')->exists($mitra->logo)) {
                Storage::disk('public')->delete($mitra->logo);
            }

            $mitra->delete();

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
