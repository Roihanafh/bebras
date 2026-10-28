<?php

namespace App\Http\Controllers;

use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    private function generateUniqueUsername(string $nama, ?string $preferredUsername = null): string
    {
        if (!empty($preferredUsername)) {
            $candidate = Str::slug($preferredUsername, '_');
        } else {
            $candidate = 'biro_' . Str::slug($nama, '_');
            if (strlen($candidate) > 20) {
                $candidate = substr($candidate, 0, 20);
            }
        }

        $username = $candidate;
        $count = 1;
        while (User::where('username', $username)->exists() || KoordinatorBiro::where('username', $username)->exists()) {
            $username = $candidate . '_' . $count;
            $count++;
        }

        return $username;
    }

    public function index()
    {
        $breadcrumbs = $this->breadcrumbs();
        return view('biro.index', compact('breadcrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            // Ambil ID representatif untuk setiap biro unik berdasarkan kode_biro
            // agar biro dengan kode/nama biro yang sama tidak ditampilkan secara duplikat
            $representativeIds = User::whereHas('roles', function ($q) {
                $q->where('name', 'biro');
            })
            ->selectRaw('MIN(id) as id')
            ->groupByRaw('COALESCE(NULLIF(kode_biro, ""), CONCAT("no_kode_", id))')
            ->pluck('id');

            $users = User::whereIn('id', $representativeIds);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('kode_biro_display', function ($row) {
                    if ($row->kode_biro) {
                        return '<span class="badge bg-label-primary fs-7">' . e($row->kode_biro) . '</span>';
                    }
                    return '<span class="text-muted fst-italic">-</span>';
                })
                ->addColumn('jumlah_koordinator', function ($row) {
                    if (!empty($row->kode_biro)) {
                        $count = KoordinatorBiro::whereHas('user', function ($q) use ($row) {
                            $q->where('kode_biro', $row->kode_biro);
                        })->count();
                    } else {
                        $count = $row->koordinatorBiro()->count();
                    }
                    return '<span class="badge bg-label-info">' . $count . ' Koordinator</span>';
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

        $oldKode = $user->kode_biro;
        $newKode = $request->kode_biro;

        // Sinkronkan update kode_biro ke seluruh akun user dengan biro yang sama
        if (!empty($oldKode)) {
            User::where('kode_biro', $oldKode)->update(['kode_biro' => $newKode]);
        } else {
            User::where('name', $user->name)
                ->whereHas('roles', fn($q) => $q->where('name', 'biro'))
                ->update(['kode_biro' => $newKode]);
        }

        $user->kode_biro = $newKode;

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
        })->findOrFail($id);

        // Ambil semua koordinator dari biro yang sama berdasarkan kode_biro
        if (!empty($user->kode_biro)) {
            $koordinator = KoordinatorBiro::whereHas('user', function ($q) use ($user) {
                $q->where('kode_biro', $user->kode_biro);
            })->orderBy('nama', 'asc')->get();
        } else {
            $koordinator = $user->koordinatorBiro()->orderBy('nama', 'asc')->get();
        }

        $koordinator->makeVisible(['password']);

        return response()->json([
            'status'      => 'success',
            'biro'        => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'kode_biro' => $user->kode_biro,
            ],
            'koordinator' => $koordinator,
        ]);
    }

    public function storeKoordinator(Request $request, $id)
    {
        $targetBiroUser = User::whereHas('roles', function ($q) {
            $q->where('name', 'biro');
        })->findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'username' => 'nullable|string|max:255|unique:users,username',
            'password' => 'nullable|string|min:4|max:255',
            'telepon'  => 'nullable|string|max:255',
        ]);

        $username = $this->generateUniqueUsername($request->nama, $request->username);
        $plainPassword = $request->filled('password') ? $request->password : 'bebras123';

        // Pastikan biro memiliki kode_biro agar koordinator konsisten terhubung
        if (empty($targetBiroUser->kode_biro)) {
            $maxKode = (int) User::whereRaw('kode_biro REGEXP "^[0-9]+$"')->max('kode_biro');
            $newKode = str_pad((string) ($maxKode + 1), 3, '0', STR_PAD_LEFT);
            $targetBiroUser->update(['kode_biro' => $newKode]);
        }

        $koordinator = DB::transaction(function () use ($targetBiroUser, $request, $username, $plainPassword) {
            // 1. Buat User baru dengan role biro sesuai seed
            $newUser = User::create([
                'name'      => $targetBiroUser->name,
                'email'     => $request->email,
                'username'  => $username,
                'password'  => Hash::make($plainPassword),
                'kode_biro' => $targetBiroUser->kode_biro,
            ]);

            $roleBiro = Role::firstOrCreate(['name' => 'biro']);
            $newUser->assignRole($roleBiro);

            // 2. Buat record KoordinatorBiro yang terikat ke user baru
            return KoordinatorBiro::create([
                'user_id'  => $newUser->id,
                'nama'     => $request->nama,
                'email'    => $request->email,
                'username' => $username,
                'password' => $plainPassword,
                'telepon'  => $request->telepon,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil ditambahkan!',
            'data'    => $koordinator,
        ]);
    }

    public function updateKoordinator(Request $request, $id)
    {
        $koordinator = KoordinatorBiro::with('user')->findOrFail($id);
        $userId = $koordinator->user_id;

        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255' . ($userId ? '|unique:users,email,' . $userId : ''),
            'username' => 'nullable|string|max:255' . ($userId ? '|unique:users,username,' . $userId : ''),
            'password' => 'nullable|string|min:4|max:255',
            'telepon'  => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($koordinator, $request) {
            $dataToUpdate = [
                'nama'    => $request->nama,
                'email'   => $request->email,
                'telepon' => $request->telepon,
            ];

            if ($request->filled('username')) {
                $dataToUpdate['username'] = $request->username;
            }
            if ($request->filled('password')) {
                $dataToUpdate['password'] = $request->password;
            }

            $koordinator->update($dataToUpdate);

            // Sinkronkan ke akun user login jika ada
            if ($koordinator->user) {
                $userUpdates = [
                    'email' => $request->email,
                ];
                if ($request->filled('username')) {
                    $userUpdates['username'] = $request->username;
                }
                if ($request->filled('password')) {
                    $userUpdates['password'] = Hash::make($request->password);
                }
                $koordinator->user->update($userUpdates);
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil diperbarui!',
            'data'    => $koordinator->fresh(),
        ]);
    }

    public function destroyKoordinator($id)
    {
        $koordinator = KoordinatorBiro::with('user')->findOrFail($id);
        $user = $koordinator->user;

        DB::transaction(function () use ($koordinator, $user) {
            $koordinator->delete();

            if ($user) {
                // Periksa apakah user masih memiliki koordinator lain
                $remainingKoordinator = KoordinatorBiro::where('user_id', $user->id)->count();
                // Periksa apakah ada user biro lain dengan kode_biro yang sama
                $hasOtherBiroUser = User::where('kode_biro', $user->kode_biro)
                    ->where('id', '!=', $user->id)
                    ->exists();

                // Hapus user jika koordinator sudah tidak ada dan bukan satu-satunya user biro
                if ($remainingKoordinator === 0 && $hasOtherBiroUser) {
                    $user->delete();
                }
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Koordinator berhasil dihapus!',
        ]);
    }
}
