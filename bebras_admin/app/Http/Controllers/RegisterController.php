<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class RegisterController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $users = User::all();
        return view('auth.register', compact('users', 'roles'));
    }

    public function getAkun(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->select('users.*'); // ambil user dengan relasi role
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return $row->roles->pluck('name')->join(', ');
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<button class="btn btn-sm btn-warning edit" data-id="' . $row->id . '">Edit</button> ';
                    $btn .= '<button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required',
            'roles'     => 'required',
            'kode_biro' => 'nullable|string|max:3',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'kode_biro' => $request->kode_biro ?: null,
        ]);

        $user->roles()->attach($request->roles);
         return response()->json([
        'message' => 'Akun berhasil ditambahkan!',
        'data'    => $user
    ]);
    }

    public function edit($id)
    {
        $user  = User::with('roles')->findOrFail($id);
        $roles = Role::all();

        return response()->json([
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role_id'   => 'required',
            'password'  => 'nullable|min:6',
            'kode_biro' => 'nullable|string|max:3',
        ]);

        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        if ($request->has('kode_biro')) {
            $user->kode_biro = $request->kode_biro ?: null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // update role (sync, bukan attach biar tidak dobel)
        $user->roles()->sync([$request->role_id]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui'
            ]);
        }

        return redirect()->route('register')
            ->with('success', 'User berhasil diperbarui');

    }

   public function destroy(User $user)
{
    try {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus user!'
        ], 500);
    }
}

}
