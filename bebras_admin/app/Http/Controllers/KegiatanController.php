<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class KegiatanController extends Controller
{
    private function breadCrumbs($currentLabel, $currentUrl = null): array
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Kegiatan', 'url' => route('kegiatan.index')],
            ['label' => $currentLabel, 'url' => $currentUrl],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadCrumbs('Daftar Kegiatan');
        return view('kegiatan.index', compact('breadcrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $kegiatan = Kegiatan::with('menuKegiatan')
                ->where('tipe', '!=', 'berita')
                ->whereNotNull('menu_kegiatan_id')
                ->orderBy('menu_kegiatan_id')
                ->orderBy('urutan');
            return DataTables::of($kegiatan)
                ->addIndexColumn()
                ->addColumn('menu_nama', fn($row) => $row->menuKegiatan?->nama_menu ?? '-')
                ->addColumn('gambar', function ($row) {
                    if ($row->gambar) {
                        $url = str_starts_with($row->gambar, 'img/')
                            ? asset($row->gambar)
                            : asset('storage/' . $row->gambar);
                        return '<img src="' . $url . '" width="60" height="45"
                                    style="object-fit:cover;border-radius:4px;"
                                    onerror="this.onerror=null;this.replaceWith(document.createTextNode(\'-\'))">';
                    }
                    return '-';
                })
                ->addColumn('actions', function ($row) {
                    $editUrl   = route('kegiatan.edit', $row->id);
                    $deleteUrl = route('kegiatan.destroy', $row->id);
                    return '
                        <div class="d-flex gap-1">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning"><i class="bx bx-edit"></i></a>
                            <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus kegiatan ini?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['gambar', 'actions'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $breadcrumbs       = $this->breadCrumbs('Tambah Kegiatan');
        $menuList          = MenuKegiatan::orderBy('urutan')->get();
        $defaultMenuId     = $request->query('menu_kegiatan_id');
        return view('kegiatan.form', compact('breadcrumbs', 'menuList', 'defaultMenuId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_kegiatan_id' => 'required|exists:menu_kegiatan,id',
            'judul'            => 'required|string|max:255',
            'deskripsi'        => 'nullable|string',
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'kota'             => 'nullable|string|max:100',
            'tanggal_lokasi'   => 'nullable|string|max:255',
            'speaker'          => 'nullable|string|max:255',
            'urutan'           => 'required|integer|min:0',
        ]);

        $validated['tipe'] = 'kegiatan_menu';
        $validated['status_validasi'] = 'approved';

        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                $validated['gambar'] = $request->file('gambar')->store('kegiatan', 'public');
            }
            Kegiatan::create($validated);
            DB::commit();

            // Redirect back to menu_kegiatan edit if we came from there
            if ($request->filled('redirect_menu_id')) {
                return redirect()->route('menu_kegiatan.edit', $request->redirect_menu_id)
                    ->with('success', 'Kegiatan berhasil ditambahkan');
            }
            return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $breadcrumbs = $this->breadCrumbs('Edit Kegiatan');
        $data        = Kegiatan::findOrFail($id);
        $menuList    = MenuKegiatan::orderBy('urutan')->get();
        return view('kegiatan.form', compact('breadcrumbs', 'data', 'menuList'));
    }

    public function update(Request $request, $id)
    {
        $data = Kegiatan::findOrFail($id);

        $validated = $request->validate([
            'menu_kegiatan_id' => 'required|exists:menu_kegiatan,id',
            'judul'            => 'required|string|max:255',
            'deskripsi'        => 'nullable|string',
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'kota'             => 'nullable|string|max:100',
            'tanggal_lokasi'   => 'nullable|string|max:255',
            'speaker'          => 'nullable|string|max:255',
            'urutan'           => 'required|integer|min:0',
        ]);

        $validated['tipe'] = 'kegiatan_menu';
        $validated['status_validasi'] = 'approved';

        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                    Storage::disk('public')->delete($data->gambar);
                }
                $validated['gambar'] = $request->file('gambar')->store('kegiatan', 'public');
            } else {
                unset($validated['gambar']);
            }
            $data->update($validated);
            DB::commit();
            return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Kegiatan::findOrFail($id);
            if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                Storage::disk('public')->delete($data->gambar);
            }
            $data->delete();
            DB::commit();

            if (url()->previous() && str_contains(url()->previous(), 'menu_kegiatan')) {
                return back()->with('success', 'Kegiatan berhasil dihapus');
            }
            return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
