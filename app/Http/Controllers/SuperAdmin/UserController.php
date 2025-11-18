<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil ID role 'siswa'
        $siswaRoleId = Role::where('code', 'siswa')->value('id');

        // Ambil user yang TIDAK memiliki role 'siswa'
        $users = User::whereDoesntHave('roles', function ($query) use ($siswaRoleId) {
                    $query->where('role_id', $siswaRoleId);
                })
                ->with('roles') // Tetap load roles untuk ditampilkan
                ->latest()
                ->paginate(10);

        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua role KECUALI 'siswa'
        $roles = Role::where('code', '!=', 'siswa')
                    ->orderBy('name')
                    ->get();
        return view('superadmin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ambil ID role 'siswa' untuk validasi
        $siswaRoleId = Role::where('code', 'siswa')->value('id');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Validasi role_id: harus ada, string, ada di tabel core_roles, dan BUKAN ID role siswa
            'role_id' => ['required', 'string', 'exists:core_roles,id', Rule::notIn([$siswaRoleId])],
        ],[
            'role_id.not_in' => 'Role Siswa tidak dapat dipilih.', // Custom error message
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->roles()->attach($request->role_id, ['id' => (string) Str::uuid()]);

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Redirect ke edit saja
        return redirect()->route('superadmin.users.edit', $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
         // Ambil semua role KECUALI 'siswa'
        $roles = Role::where('code', '!=', 'siswa')
                     ->orderBy('name')
                     ->get();
        $userRole = $user->roles()->first(); // Ambil role user saat ini

        // Jika user yang diedit memiliki role siswa (seharusnya tidak terjadi tapi untuk jaga-jaga)
        // Sebaiknya redirect atau tampilkan pesan error
         if ($userRole && $userRole->code === 'siswa') {
             return redirect()->route('superadmin.users.index')
                        ->with('error', 'User dengan role Siswa tidak dapat diedit dari menu ini.');
         }

        return view('superadmin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Ambil ID role 'siswa' untuk validasi
        $siswaRoleId = Role::where('code', 'siswa')->value('id');

         $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
             // Validasi role_id: harus ada, string, ada di tabel core_roles, dan BUKAN ID role siswa
            'role_id' => ['required', 'string', 'exists:core_roles,id', Rule::notIn([$siswaRoleId])],
        ],[
            'role_id.not_in' => 'Role Siswa tidak dapat dipilih.', // Custom error message
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        $user->roles()->detach(); 
        $user->roles()->attach($request->role_id, ['id' => (string) Str::uuid()]);
        
        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
             return redirect()->route('superadmin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Tambahan: Pastikan tidak bisa menghapus user dengan role siswa dari sini
        if ($user->roles->contains('code', 'siswa')) {
             return redirect()->route('superadmin.users.index')
                        ->with('error', 'User dengan role Siswa tidak dapat dihapus dari menu ini.');
        }


        try {
            $user->delete();
            return redirect()->route('superadmin.users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
             return redirect()->route('superadmin.users.index')->with('error', 'Gagal menghapus user. Error: ' . $e->getMessage());
        }
    }
}