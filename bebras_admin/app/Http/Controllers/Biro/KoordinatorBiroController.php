<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $user = auth()->user();
        if (!empty($user->kode_biro)) {
            $totalKoordinator = KoordinatorBiro::whereHas('user', function ($q) use ($user) {
                $q->where('kode_biro', $user->kode_biro);
            })->count();
        } else {
            $totalKoordinator = $user->koordinatorBiro()->count();
        }

        $breadcrumbs = $this->breadCrumbs('Daftar Koordinator');

        return view('biro.koordinator', compact('user', 'totalKoordinator', 'breadcrumbs'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            // Ambil seluruh koordinator dari biro yang sama berdasarkan kode_biro
            if (!empty($user->kode_biro)) {
                $koordinators = KoordinatorBiro::whereHas('user', function ($q) use ($user) {
                    $q->where('kode_biro', $user->kode_biro);
                })->orderBy('nama', 'asc');
            } else {
                $koordinators = $user->koordinatorBiro()->orderBy('nama', 'asc');
            }

            return DataTables::of($koordinators)
                ->addIndexColumn()
                ->addColumn('email_display', function ($row) {
                    return '<a href="mailto:' . e($row->email) . '" class="text-primary">' . e($row->email) . '</a>';
                })
                ->addColumn('username_display', function ($row) {
                    if ($row->username) {
                        return '<span class="badge bg-label-info font-monospace">' . e($row->username) . '</span>';
                    }
                    return '<span class="text-muted fst-italic">-</span>';
                })
                ->addColumn('telepon_display', function ($row) {
                    return e($row->telepon ?? '-');
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<button type="button" class="btn btn-sm btn-warning btn-edit-koordinator me-1" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '" data-email="' . e($row->email) . '" data-username="' . e($row->username ?? '') . '" data-telepon="' . e($row->telepon ?? '') . '">';
                    $btn .= '<i class="bx bx-edit-alt me-1"></i>Edit';
                    $btn .= '</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete-koordinator" data-id="' . $row->id . '" data-nama="' . e($row->nama) . '">';
                    $btn .= '<i class="bx bx-trash me-1"></i>Hapus';
                    $btn .= '</button>';
                    return $btn;
                })
                ->rawColumns(['email_display', 'username_display', 'aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $targetBiroUser = auth()->user();

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

    public function edit($id)
    {
        $user = auth()->user();
        if (!empty($user->kode_biro)) {
            $koordinator = KoordinatorBiro::whereHas('user', function ($q) use ($user) {
                $q->where('kode_biro', $user->kode_biro);
            })->findOrFail($id);
        } else {
            $koordinator = KoordinatorBiro::where('user_id', $user->id)->findOrFail($id);
        }

        $koordinator->makeVisible(['password']);

        return response()->json([
            'status' => 'success',
            'data'   => $koordinator,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!empty($user->kode_biro)) {
            $koordinator = KoordinatorBiro::whereHas('user', function ($q) use ($user) {
                $q->where('kode_biro', $user->kode_biro);
            })->findOrFail($id);
        } else {
            $koordinator = KoordinatorBiro::where('user_id', $user->id)->findOrFail($id);
        }

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

    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!empty($authUser->kode_biro)) {
            $koordinator = KoordinatorBiro::whereHas('user', function ($q) use ($authUser) {
                $q->where('kode_biro', $authUser->kode_biro);
            })->findOrFail($id);
        } else {
            $koordinator = KoordinatorBiro::where('user_id', $authUser->id)->findOrFail($id);
        }

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
