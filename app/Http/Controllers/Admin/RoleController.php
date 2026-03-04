<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected function getPermissionGroups(): array
    {
        return [
            'pages' => ['label' => 'Sayfalar', 'actions' => ['view', 'create', 'edit', 'delete']],
            'articles' => ['label' => 'Yazılar', 'actions' => ['view', 'create', 'edit', 'delete']],
            'menus' => ['label' => 'Menüler', 'actions' => ['view', 'create', 'edit', 'delete']],
            'forms' => ['label' => 'Formlar', 'actions' => ['view', 'create', 'edit', 'delete']],
            'media' => ['label' => 'Medya', 'actions' => ['view', 'upload', 'edit', 'delete']],
            'seo' => ['label' => 'SEO', 'actions' => ['view', 'edit', 'delete']],
            'redirects' => ['label' => 'Yönlendirmeler', 'actions' => ['view', 'create', 'edit', 'delete']],
            'reviews' => ['label' => 'Yorumlar', 'actions' => ['view', 'edit', 'delete']],
            'members' => ['label' => 'Üyeler', 'actions' => ['view', 'create', 'edit', 'delete']],
            'languages' => ['label' => 'Diller', 'actions' => ['view', 'create', 'edit', 'delete']],
            'settings' => ['label' => 'Ayarlar', 'actions' => ['view', 'edit']],
            'admins' => ['label' => 'Yöneticiler', 'actions' => ['view', 'create', 'edit', 'delete']],
            'roles' => ['label' => 'Roller', 'actions' => ['view', 'create', 'edit', 'delete']],
            'design' => ['label' => 'Tasarım (CSS/JS)', 'actions' => ['view', 'edit']],
            'maintenance' => ['label' => 'Bakım', 'actions' => ['view', 'execute']],
        ];
    }

    public function index()
    {
        $roles = Role::where('guard_name', 'admin')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->getPermissionGroups();

        // Ensure all permissions exist
        $this->ensurePermissionsExist($permissionGroups);

        $allPermissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();

        return view('admin.roles.create', compact('permissionGroups', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'admin',
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol başarıyla oluşturuldu.');
    }

    public function edit(Role $role)
    {
        $permissionGroups = $this->getPermissionGroups();
        $this->ensurePermissionsExist($permissionGroups);

        $allPermissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissionGroups', 'allPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol başarıyla güncellendi.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Bu role atanmış kullanıcılar var. Önce kullanıcıların rollerini değiştirin.');
        }

        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin rolü silinemez.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rol başarıyla silindi.');
    }

    protected function ensurePermissionsExist(array $groups): void
    {
        foreach ($groups as $group => $config) {
            foreach ($config['actions'] as $action) {
                Permission::firstOrCreate([
                    'name' => "{$group}.{$action}",
                    'guard_name' => 'admin',
                ]);
            }
        }
    }
}
