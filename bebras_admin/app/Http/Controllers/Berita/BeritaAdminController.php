<?php

namespace App\Http\Controllers\Berita;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BeritaAdminController extends Controller
{
    private function breadCrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Manajemen Berita', 'url' => route('admin.berita.index')],
            ['label' => $currentLabel],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadCrumbs('Daftar Semua Berita');

        return view('berita-admin.index', compact('breadcrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $berita = Kegiatan::with('dibuatOleh')
                ->where('tipe', 'berita')
                ->orderBy('created_at', 'desc');

            return DataTables::of($berita)
                ->addIndexColumn()
                ->addColumn('nama_biro', fn($row) => $row->dibuatOleh?->name ?? '-')
                ->addColumn('status_badge', function ($row) {
                    return match ($row->status_validasi) {
                        'approved' => '<span class="badge bg-success">Approved</span>',
                        'rejected' => '<span class="badge bg-danger">Rejected</span>',
                        default    => '<span class="badge bg-warning text-dark">Pending</span>',
                    };
                })
                ->addColumn('tanggal', fn($row) => $row->created_at?->format('d M Y') ?? '-')
                ->addColumn('actions', function ($row) {
                    $editUrl    = route('admin.berita.edit', $row->id);
                    $approveUrl = route('admin.berita.approve', $row->id);
                    $rejectUrl  = route('admin.berita.reject', $row->id);
                    $deleteUrl  = route('admin.berita.destroy', $row->id);

                    $statusLabel = match ($row->status_validasi) {
                        'approved' => 'sudah disetujui',
                        'rejected' => 'sudah ditolak',
                        default    => null,
                    };

                    $approveConfirm = $statusLabel
                        ? "Berita ini {$statusLabel}. Yakin ingin menyetujui ulang?"
                        : 'Setujui berita ini?';

                    $rejectConfirm = $statusLabel
                        ? "Berita ini {$statusLabel}. Yakin ingin menolak ulang?"
                        : 'Tolak berita ini?';

                    return '
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="' . $approveUrl . '" method="POST"
                                  onsubmit="return confirm(\'' . addslashes($approveConfirm) . '\')">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                    <i class="bx bx-check"></i>
                                </button>
                            </form>
                            <form action="' . $rejectUrl . '" method="POST"
                                  onsubmit="return confirm(\'' . addslashes($rejectConfirm) . '\')">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-sm btn-secondary" title="Tolak">
                                    <i class="bx bx-x"></i>
                                </button>
                            </form>
                            <form action="' . $deleteUrl . '" method="POST"
                                  onsubmit="return confirm(\'Hapus berita ini secara permanen?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }
    }

    public function edit(int $id)
    {
        $breadcrumbs = $this->breadCrumbs('Edit Berita');
        $data        = Kegiatan::findOrFail($id);
        $menuList    = MenuKegiatan::orderBy('urutan')->get();

        return view('berita-admin.form', compact('breadcrumbs', 'data', 'menuList'));
    }

    public function update(Request $request, int $id)
    {
        $data = Kegiatan::findOrFail($id);

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

        // Tidak menyentuh status_validasi — hanya diubah melalui approve/reject
        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                    Storage::disk('public')->delete($data->gambar);
                }
                $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
            } else {
                unset($validated['gambar']);
            }

            $data->update($validated);
            DB::commit();

            return redirect()
                ->route('admin.berita.index')
                ->with('success', 'Berita berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.'])
                ->withInput();
        }
    }

    public function approve(int $id)
    {
        $berita = Kegiatan::findOrFail($id);

        DB::beginTransaction();
        try {
            $berita->status_validasi  = 'approved';
            $berita->divalidasi_oleh  = auth()->user()?->id;
            $berita->divalidasi_pada  = now();
            $berita->save();
            DB::commit();

            return redirect()
                ->route('admin.berita.index')
                ->with('success', 'Berita berhasil disetujui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function reject(Request $request, int $id)
    {
        $request->validate([
            'catatan_validasi' => 'nullable|string',
        ]);

        $berita = Kegiatan::findOrFail($id);

        DB::beginTransaction();
        try {
            $berita->status_validasi  = 'rejected';
            $berita->divalidasi_oleh  = auth()->user()?->id;
            $berita->divalidasi_pada  = now();
            $berita->catatan_validasi = $request->catatan_validasi;
            $berita->save();
            DB::commit();

            return redirect()
                ->route('admin.berita.index')
                ->with('success', 'Berita berhasil ditolak.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $berita = Kegiatan::findOrFail($id);

            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }

            $berita->delete();
            DB::commit();

            return redirect()
                ->route('admin.berita.index')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }
}
