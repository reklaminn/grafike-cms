<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::with('roles')->withTrashed(false);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->orderBy('name')->paginate(25);

        return view('admin.admin-users.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();

        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $admin = Admin::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if (!empty($data['role'])) {
            $admin->assignRole($data['role']);
        }

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Yönetici başarıyla oluşturuldu.');
    }

    public function edit(Admin $admin_user)
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        $admin_user->load('roles');

        return view('admin.admin-users.edit', compact('admin_user', 'roles'));
    }

    public function update(Request $request, Admin $admin_user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('admins', 'username')->ignore($admin_user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin_user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $admin_user->name = $data['name'];
        $admin_user->username = $data['username'];
        $admin_user->email = $data['email'];

        if (!empty($data['password'])) {
            $admin_user->password = $data['password'];
        }

        $admin_user->save();

        // Sync role
        $admin_user->syncRoles($data['role'] ? [$data['role']] : []);

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Yönetici başarıyla güncellendi.');
    }

    public function destroy(Admin $admin_user)
    {
        // Prevent self-deletion
        if ($admin_user->id === auth('admin')->id()) {
            return back()->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        $admin_user->delete();

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Yönetici başarıyla silindi.');
    }

    public function toggleStatus(Admin $admin_user)
    {
        if ($admin_user->id === auth('admin')->id()) {
            return back()->with('error', 'Kendi hesabınızı devre dışı bırakamazsınız.');
        }

        if ($admin_user->trashed()) {
            $admin_user->restore();
            $message = 'Yönetici aktif edildi.';
        } else {
            $admin_user->delete();
            $message = 'Yönetici devre dışı bırakıldı.';
        }

        return back()->with('success', $message);
    }
}
