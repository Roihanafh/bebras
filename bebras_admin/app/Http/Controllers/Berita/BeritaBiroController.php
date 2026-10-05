<?php

namespace App\Http\Controllers\Berita;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BeritaBiroController extends Controller
{
    private function breadCrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Berita', 'url' => route('berita.index')],
            ['label' => $currentLabel],
        ];
    }

    public function index()
    {
        $userId = auth()->id();

        $totalPending  = Kegiatan::where('tipe', 'berita')
            ->where('dibuat_oleh', $userId)
            ->where('status_validasi', 'pending')
            ->count();

        $totalApproved = Kegiatan::where('tipe', 'berita')
            ->where('dibuat_oleh', $userId)
            ->where('status_validasi', 'approved')
            ->count();

        $totalRejected = Kegiatan::where('tipe', 'berita')
            ->where('dibuat_oleh', $userId)
            ->where('status_validasi', 'rejected')
            ->count();

        $breadcrumbs = $this->breadCrumbs('Daftar Berita');

        return view('berita.index', compact(
            'breadcrumbs',
            'totalPending',
            'totalApproved',
            'totalRejected'
        ));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = Kegiatan::with('menuKegiatan')
                ->where('tipe', 'berita')
                ->where('dibuat_oleh', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('judul', fn($row) => $row->judul)
                ->addColumn('menu_kegiatan', fn($row) => $row->menuKegiatan?->nama_menu ?? '-')
                ->addColumn('status_validasi', function ($row) {
                    return match ($row->status_validasi) {
                        'pending'  => '<span class="badge bg-warning text-dark">Pending</span>',
                        'approved' => '<span class="badge bg-success">Approved</span>',
                        'rejected' => '<span class="badge bg-danger">Rejected</span>',
                        default    => '<span class="badge bg-secondary">' . e($row->status_validasi) . '</span>',
                    };
                })
                ->addColumn('catatan_admin', function ($row) {
                    if ($row->status_validasi === 'rejected') {
                        return $row->catatan_validasi
                            ? e($row->catatan_validasi)
                            : '<em class="text-muted">Belum ada catatan dari admin</em>';
                    }
                    return '-';
                })
                ->addColumn('tanggal', fn($row) => $row->created_at?->format('d M Y') ?? '-')
                ->addColumn('aksi', function ($row) {
                    $editUrl   = route('berita.edit', $row->id);
                    $deleteUrl = route('berita.destroy', $row->id);
                    return '
                        <div class="d-flex gap-1">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="' . $deleteUrl . '" method="POST"
                                  onsubmit="return confirm(\'Hapus berita ini?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['status_validasi', 'catatan_admin', 'aksi'])
                ->make(true);
        }
    }

    public function create()
    {
        $breadcrumbs = $this->breadCrumbs('Tambah Berita');
        $menuList    = MenuKegiatan::orderBy('urutan')->get();

        return view('berita.form', compact('breadcrumbs', 'menuList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_kegiatan_id' => 'required|exists:menu_kegiatan,id',
            'judul'            => 'required|string|max:255',
            'deskripsi'        => 'required|string',
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kota'             => 'nullable|string|max:255',
            'tanggal_lokasi'   => 'nullable|date',
            'speaker'          => 'nullable|string|max:255',
            'urutan'           => 'nullable|integer',
        ]);

        $validated['tipe']            = 'berita';
        $validated['status_validasi'] = 'pending';
        $validated['dibuat_oleh']     = auth()->id();

        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
            }

            Kegiatan::create($validated);
            DB::commit();

            return redirect()
                ->route('berita.index')
                ->with('success', 'Berita berhasil ditambahkan dan menunggu persetujuan admin.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.'])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $berita = Kegiatan::findOrFail($id);

        if ($berita->dibuat_oleh !== auth()->id()) {
            abort(403);
        }

        $breadcrumbs = $this->breadCrumbs('Edit Berita');
        $menuList    = MenuKegiatan::orderBy('urutan')->get();

        return view('berita.form', compact('breadcrumbs', 'menuList', 'berita'));
    }

    public function update(Request $request, $id)
    {
        $berita = Kegiatan::findOrFail($id);

        if ($berita->dibuat_oleh !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'menu_kegiatan_id' => 'required|exists:menu_kegiatan,id',
            'judul'            => 'required|string|max:255',
            'deskripsi'        => 'required|string',
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kota'             => 'nullable|string|max:255',
            'tanggal_lokasi'   => 'nullable|date',
            'speaker'          => 'nullable|string|max:255',
            'urutan'           => 'nullable|integer',
        ]);

        // Selalu reset status ke pending saat biro mengedit (req 3.3)
        $validated['status_validasi'] = 'pending';

        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                    Storage::disk('public')->delete($berita->gambar);
                }
                $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
            } else {
                unset($validated['gambar']);
            }

            $berita->update($validated);
            DB::commit();

            return redirect()
                ->route('berita.index')
                ->with('success', 'Berita berhasil diperbarui dan menunggu persetujuan ulang dari admin.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.'])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $berita = Kegiatan::findOrFail($id);

        if ($berita->dibuat_oleh !== auth()->id()) {
            abort(403);
        }

        // Berita yang sudah disetujui tidak dapat dihapus (req 3.5)
        if ($berita->status_validasi === 'approved') {
            return back()->withErrors(['error' => 'Berita yang sudah disetujui tidak dapat dihapus.']);
        }

        DB::beginTransaction();
        try {
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }

            $berita->delete();
            DB::commit();

            return redirect()
                ->route('berita.index')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }
}
