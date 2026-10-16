<?php

namespace App\Http\Controllers;

use App\Models\AnggotaBiro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AnggotaBiroAdminController extends Controller
{
    private function breadcrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Anggota Biro', 'url' => route('admin.anggota_biro.index')],
            ['label' => $currentLabel],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadcrumbs('Daftar Anggota Biro');

        $biros = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->orderBy('name', 'asc')->get();

        $totalAll = AnggotaBiro::count();
        $totalPending = AnggotaBiro::where('status_validasi', 'pending')->count();
        $totalApproved = AnggotaBiro::where('status_validasi', 'approved')->count();
        $totalRejected = AnggotaBiro::where('status_validasi', 'rejected')->count();

        return view('admin.anggota_biro.index', compact(
            'breadcrumbs',
            'biros',
            'totalAll',
            'totalPending',
            'totalApproved',
            'totalRejected'
        ));
    }

    public function list(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = AnggotaBiro::with('biro')->select('anggota_biro.*');

            if ($request->filled('biro_id')) {
                $query->where('biro_id', $request->biro_id);
            }

            if ($request->filled('status_validasi')) {
                $query->where('status_validasi', $request->status_validasi);
            }

            $query->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('no_anggota_display', function ($row) {
                    return '<span class="badge bg-label-primary fs-7 fw-bold font-monospace">' . e($row->no_anggota) . '</span>';
                })
                ->addColumn('biro_display', function ($row) {
                    $html = '<div class="fw-semibold text-dark">' . e($row->biro->name ?? 'Biro Tidak Ditemukan') . '</div>';
                    if (!empty($row->biro->kode_biro)) {
                        $html .= '<small class="text-muted">Kode: <span class="badge bg-label-info">' . e($row->biro->kode_biro) . '</span></small>';
                    }
                    return $html;
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
                    return '<span class="badge bg-warning text-dark"><i class="bx bx-time-five me-1"></i>Pending</span>';
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<div class="d-flex flex-wrap gap-1">';

                    if ($row->status_validasi === 'pending') {
                        $btn .= '<button type="button" class="btn btn-xs btn-success btn-approve" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" title="Setujui">';
                        $btn .= '<i class="bx bx-check me-1"></i>Setujui';
                        $btn .= '</button>';
                        $btn .= '<button type="button" class="btn btn-xs btn-danger btn-reject" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" title="Tolak">';
                        $btn .= '<i class="bx bx-x me-1"></i>Tolak';
                        $btn .= '</button>';
                    } elseif ($row->status_validasi === 'rejected') {
                        $btn .= '<button type="button" class="btn btn-xs btn-outline-success btn-approve" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" title="Setujui">';
                        $btn .= '<i class="bx bx-check me-1"></i>Setujui';
                        $btn .= '</button>';
                    } elseif ($row->status_validasi === 'approved') {
                        $btn .= '<button type="button" class="btn btn-xs btn-outline-danger btn-reject" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" title="Tolak">';
                        $btn .= '<i class="bx bx-x me-1"></i>Tolak';
                        $btn .= '</button>';
                    }

                    $btn .= '<button type="button" class="btn btn-xs btn-warning btn-edit-anggota" data-id="' . $row->id . '" title="Edit">';
                    $btn .= '<i class="bx bx-edit-alt"></i>';
                    $btn .= '</button>';

                    $btn .= '<button type="button" class="btn btn-xs btn-danger btn-delete-anggota" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" title="Hapus">';
                    $btn .= '<i class="bx bx-trash"></i>';
                    $btn .= '</button>';

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['no_anggota_display', 'biro_display', 'status_display', 'aksi'])
                ->make(true);
        }
    }

    public function getNextNo($biroId)
    {
        $biro = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->findOrFail($biroId);

        $nextNo = AnggotaBiro::generateNextNoAnggota($biro->id);

        return response()->json([
            'status'  => 'success',
            'next_no' => $nextNo,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'biro_id'         => ['required', 'exists:users,id'],
            'nama'            => ['required', 'string', 'max:255'],
            'tahun_masuk'     => ['required', 'integer', 'min:1900', 'max:2100'],
            'tanggal_lahir'   => ['required', 'date'],
            'no_anggota'      => ['nullable', 'string', 'regex:/^\d{1,3}$/'],
            'status_validasi' => ['nullable', 'in:pending,approved,rejected'],
        ], [
            'biro_id.required'       => 'Pilih biro terlebih dahulu.',
            'nama.required'          => 'Nama anggota wajib diisi.',
            'tahun_masuk.required'   => 'Tahun masuk wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'no_anggota.regex'       => 'Nomor anggota harus berupa angka maksimal 3 digit (contoh: 001).',
        ]);

        $biro = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->findOrFail($request->biro_id);

        if (!empty($request->no_anggota)) {
            $noAnggota = str_pad($request->no_anggota, 3, '0', STR_PAD_LEFT);
        } else {
            $noAnggota = AnggotaBiro::generateNextNoAnggota($biro->id);
        }

        // Validasi composite unique (biro_id, no_anggota)
        $exists = AnggotaBiro::where('biro_id', $biro->id)
            ->where('no_anggota', $noAnggota)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nomor anggota ' . $noAnggota . ' sudah digunakan oleh ' . $biro->name . '. Silakan gunakan nomor lain.',
                'errors'  => [
                    'no_anggota' => ['Nomor anggota ' . $noAnggota . ' sudah terdaftar pada biro ini.'],
                ],
            ], 422);
        }

        $statusValidasi = $request->status_validasi ?? 'approved';

        $anggota = AnggotaBiro::create([
            'biro_id'         => $biro->id,
            'nama'            => $request->nama,
            'tahun_masuk'     => $request->tahun_masuk,
            'tanggal_lahir'   => $request->tanggal_lahir,
            'no_anggota'      => $noAnggota,
            'status_validasi' => $statusValidasi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Anggota biro berhasil ditambahkan!',
            'data'    => $anggota,
        ]);
    }

    public function edit($id)
    {
        $anggota = AnggotaBiro::with('biro')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $anggota->id,
                'biro_id'         => $anggota->biro_id,
                'biro_name'       => $anggota->biro->name ?? '',
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
        $anggota = AnggotaBiro::findOrFail($id);

        $request->validate([
            'biro_id'         => ['required', 'exists:users,id'],
            'nama'            => ['required', 'string', 'max:255'],
            'tahun_masuk'     => ['required', 'integer', 'min:1900', 'max:2100'],
            'tanggal_lahir'   => ['required', 'date'],
            'no_anggota'      => ['required', 'string', 'regex:/^\d{1,3}$/'],
            'status_validasi' => ['required', 'in:pending,approved,rejected'],
        ], [
            'biro_id.required'       => 'Pilih biro terlebih dahulu.',
            'nama.required'          => 'Nama anggota wajib diisi.',
            'tahun_masuk.required'   => 'Tahun masuk wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'no_anggota.required'    => 'Nomor anggota wajib diisi.',
            'no_anggota.regex'       => 'Nomor anggota harus berupa angka maksimal 3 digit (contoh: 001).',
        ]);

        $biro = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->findOrFail($request->biro_id);

        $noAnggota = str_pad($request->no_anggota, 3, '0', STR_PAD_LEFT);

        // Validasi composite unique (biro_id, no_anggota) abaikan id saat ini
        $exists = AnggotaBiro::where('biro_id', $biro->id)
            ->where('no_anggota', $noAnggota)
            ->where('id', '!=', $anggota->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nomor anggota ' . $noAnggota . ' sudah digunakan oleh ' . $biro->name . '. Silakan gunakan nomor lain.',
                'errors'  => [
                    'no_anggota' => ['Nomor anggota ' . $noAnggota . ' sudah terdaftar pada biro ini.'],
                ],
            ], 422);
        }

        $anggota->update([
            'biro_id'         => $biro->id,
            'nama'            => $request->nama,
            'tahun_masuk'     => $request->tahun_masuk,
            'tanggal_lahir'   => $request->tanggal_lahir,
            'no_anggota'      => $noAnggota,
            'status_validasi' => $request->status_validasi,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data anggota biro berhasil diperbarui!',
            'data'    => $anggota,
        ]);
    }

    public function approve($id)
    {
        $anggota = AnggotaBiro::findOrFail($id);
        $anggota->status_validasi = 'approved';
        $anggota->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan anggota ' . $anggota->nama . ' berhasil disetujui!',
            'data'    => $anggota,
        ]);
    }

    public function reject($id)
    {
        $anggota = AnggotaBiro::findOrFail($id);
        $anggota->status_validasi = 'rejected';
        $anggota->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan anggota ' . $anggota->nama . ' telah ditolak.',
            'data'    => $anggota,
        ]);
    }

    public function destroy($id)
    {
        $anggota = AnggotaBiro::findOrFail($id);
        $nama = $anggota->nama;
        $anggota->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Anggota ' . $nama . ' berhasil dihapus!',
        ]);
    }
}
