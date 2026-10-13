<?php

namespace App\Http\Controllers\Pengumuman;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PengumumanAdminController extends Controller
{
    private function breadcrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Pengumuman', 'url' => route('admin.pengumuman.index')],
            ['label' => $currentLabel],
        ];
    }

    public function index()
    {
        return view('pengumuman-admin.index', [
            'breadcrumbs' => $this->breadcrumbs('Daftar Pengumuman'),
        ]);
    }

    public function list(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $pengumumans = Kegiatan::with('dibuatOleh')
            ->where('tipe', 'pengumuman')
            ->orderByDesc('created_at');

        return DataTables::of($pengumumans)
            ->addIndexColumn()
            ->addColumn('status_popup', fn($row) => $row->is_popup
                ? '<span class="badge bg-info">Popup</span>'
                : '<span class="badge bg-secondary">Tidak</span>')
            ->addColumn('tanggal', fn($row) => $row->created_at?->format('d M Y') ?? '-')
            ->addColumn('actions', function ($row) {
                $editUrl = route('admin.pengumuman.edit', $row->id);
                $deleteUrl = route('admin.pengumuman.destroy', $row->id);

                return '<div class="d-flex gap-1">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Edit">
                        <i class="bx bx-edit"></i>
                    </a>
                    <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus pengumuman ini?\')">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['status_popup', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return view('pengumuman-admin.form', [
            'breadcrumbs' => $this->breadcrumbs('Tambah Pengumuman'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['tipe'] = 'pengumuman';
        $validated['status_validasi'] = 'approved';
        $validated['dibuat_oleh'] = auth()->user()?->id;
        $validated['is_popup'] = $request->boolean('is_popup');

        DB::transaction(function () use ($request, &$validated) {
            if ($validated['is_popup']) {
                Kegiatan::where('tipe', 'pengumuman')->update(['is_popup' => false]);
            }

            if ($request->hasFile('gambar')) {
                $validated['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
            }

            Kegiatan::create($validated);
        });

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return view('pengumuman-admin.form', [
            'breadcrumbs' => $this->breadcrumbs('Edit Pengumuman'),
            'data' => $this->findPengumuman($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $this->findPengumuman($id);
        $validated = $this->validated($request);
        $validated['is_popup'] = $request->boolean('is_popup');

        DB::transaction(function () use ($request, $data, &$validated) {
            if ($validated['is_popup']) {
                Kegiatan::where('tipe', 'pengumuman')
                    ->where('id', '!=', $data->id)
                    ->update(['is_popup' => false]);
            }

            if ($request->hasFile('gambar')) {
                if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                    Storage::disk('public')->delete($data->gambar);
                }
                $validated['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
            } else {
                unset($validated['gambar']);
            }

            $data->update($validated);
        });

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $data = $this->findPengumuman($id);

        if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
            Storage::disk('public')->delete($data->gambar);
        }

        $data->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kota' => 'nullable|string|max:255',
            'tanggal_lokasi' => 'nullable|date',
            'speaker' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ]);
    }

    private function findPengumuman(int $id): Kegiatan
    {
        return Kegiatan::where('tipe', 'pengumuman')->findOrFail($id);
    }
}
