<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\AnggotaBiro;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AnggotaBiroController extends Controller
{
    private function breadcrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Berita', 'url' => route('berita.index')],
            ['label' => 'Anggota Biro', 'url' => route('biro_anggota.index')],
            ['label' => $currentLabel],
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $totalAll = $user->anggotaBiro()->count();
        $totalPending = $user->anggotaBiro()->where('status_validasi', 'pending')->count();
        $totalApproved = $user->anggotaBiro()->where('status_validasi', 'approved')->count();
        $totalRejected = $user->anggotaBiro()->where('status_validasi', 'rejected')->count();
        $nextNo = AnggotaBiro::generateNextNoAnggota($user->id);

        $breadcrumbs = $this->breadcrumbs('Daftar Anggota Saya');

        return view('biro.anggota', compact(
            'user',
            'totalAll',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'nextNo',
            'breadcrumbs'
        ));
    }

    public function list(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $user = auth()->user();
            $query = $user->anggotaBiro()->select('anggota_biro.*');

            if ($request->filled('status_validasi')) {
                $query->where('status_validasi', $request->status_validasi);
            }

            $query->orderBy('no_anggota', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('no_anggota_display', function ($row) {
                    return '<span class="badge bg-label-primary fs-7 fw-bold font-monospace">' . e($row->no_anggota) . '</span>';
                })
                ->addColumn('tanggal_lahir_display', function ($row) {
                    return $row->tanggal_lahir ? $row->tanggal_lahir->format('d/m/Y') : '-';
                })
                ->addColumn('status_display', function ($row) {
                    if ($row->status_validasi === 'approved') {
                        return '<span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Disetujui</span>';
                    } elseif ($row->status_validasi === 'rejected') {
                        return '<span class="badge bg-danger"><i class="bx bx-x-circle me-1"></i>Ditolak</span>';
                    }
                    return '<span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>Pending (Menunggu Validasi)</span>';
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<div class="d-flex gap-1">';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning btn-edit-anggota" data-id="' . $row->id . '">';
                    $btn .= '<i class="bx bx-edit-alt me-1"></i>Edit';
                    $btn .= '</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete-anggota" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '">';
                    $btn .= '<i class="bx bx-trash me-1"></i>Hapus';
                    $btn .= '</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['no_anggota_display', 'status_display', 'aksi'])
                ->make(true);
        }
    }

    public function getNextNo()
    {
        $nextNo = AnggotaBiro::generateNextNoAnggota(auth()->id());

        return response()->json([
            'status'  => 'success',
            'next_no' => $nextNo,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'tahun_masuk'   => ['required', 'integer', 'min:1900', 'max:2100'],
            'tanggal_lahir' => ['required', 'date'],
            'no_anggota'    => ['nullable', 'string', 'regex:/^\d{1,3}$/'],
        ], [
            'nama.required'          => 'Nama anggota wajib diisi.',
            'tahun_masuk.required'   => 'Tahun masuk wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'no_anggota.regex'       => 'Nomor anggota harus berupa angka maksimal 3 digit (contoh: 001).',
        ]);

        $biroId = auth()->id();

        if (!empty($request->no_anggota)) {
            $noAnggota = str_pad($request->no_anggota, 3, '0', STR_PAD_LEFT);
        } else {
            $noAnggota = AnggotaBiro::generateNextNoAnggota($biroId);
        }

        // Cek composite unique (biro_id, no_anggota)
        $exists = AnggotaBiro::where('biro_id', $biroId)
            ->where('no_anggota', $noAnggota)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nomor anggota ' . $noAnggota . ' sudah digunakan oleh biro Anda. Silakan gunakan nomor lain.',
                'errors'  => [
                    'no_anggota' => ['Nomor anggota ' . $noAnggota . ' sudah terdaftar pada biro Anda.'],
                ],
            ], 422);
        }

        // Status pengajuan dari biro harus otomatis 'pending'
        $anggota = AnggotaBiro::create([
            'biro_id'         => $biroId,
            'nama'            => $request->nama,
            'tahun_masuk'     => $request->tahun_masuk,
            'tanggal_lahir'   => $request->tanggal_lahir,
            'no_anggota'      => $noAnggota,
            'status_validasi' => 'pending',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan anggota berhasil dikirim dan menunggu validasi Admin!',
            'data'    => $anggota,
        ]);
    }

    public function edit($id)
    {
        // Hanya mencari anggota yang berelasi dengan biro yang sedang login
        $anggota = AnggotaBiro::where('biro_id', auth()->id())->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $anggota->id,
                'nama'            => $anggota->nama,
                'tahun_masuk'     => $anggota->tahun_masuk,
                'tanggal_lahir'   => $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('Y-m-d') : '',
                'no_anggota'      => $anggota->no_anggota,
                'status_validasi' => $anggota->status_validasi,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        // Hanya mengupdate anggota milik biro yang sedang login
        $anggota = AnggotaBiro::where('biro_id', auth()->id())->findOrFail($id);

        $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'tahun_masuk'   => ['required', 'integer', 'min:1900', 'max:2100'],
            'tanggal_lahir' => ['required', 'date'],
            'no_anggota'    => ['required', 'string', 'regex:/^\d{1,3}$/'],
        ], [
            'nama.required'          => 'Nama anggota wajib diisi.',
            'tahun_masuk.required'   => 'Tahun masuk wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'no_anggota.required'    => 'Nomor anggota wajib diisi.',
            'no_anggota.regex'       => 'Nomor anggota harus berupa angka maksimal 3 digit (contoh: 001).',
        ]);

        $biroId = auth()->id();
        $noAnggota = str_pad($request->no_anggota, 3, '0', STR_PAD_LEFT);

        $exists = AnggotaBiro::where('biro_id', $biroId)
            ->where('no_anggota', $noAnggota)
            ->where('id', '!=', $anggota->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nomor anggota ' . $noAnggota . ' sudah digunakan oleh biro Anda. Silakan gunakan nomor lain.',
                'errors'  => [
                    'no_anggota' => ['Nomor anggota ' . $noAnggota . ' sudah terdaftar pada biro Anda.'],
                ],
            ], 422);
        }

        // Biro TIDAK diperbolehkan mengubah status_validasi
        $anggota->update([
            'nama'          => $request->nama,
            'tahun_masuk'   => $request->tahun_masuk,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_anggota'    => $noAnggota,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data anggota berhasil diperbarui!',
            'data'    => $anggota,
        ]);
    }

    public function destroy($id)
    {
        // Hanya menghapus anggota milik biro yang sedang login
        $anggota = AnggotaBiro::where('biro_id', auth()->id())->findOrFail($id);
        $nama = $anggota->nama;
        $anggota->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Anggota ' . $nama . ' berhasil dihapus!',
        ]);
    }
}
