<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::pluck('name');
        $adminCount = User::role('admin')->count();

        return view('admin.users.index', compact('users', 'roles', 'adminCount'));
    }

    public function show(User $user)
    {
        $availablePermissions = [
            'access dashboard',
            'manage transactions',
            'manage accounts',
            'manage budgets',
            'manage goals',
            'view reports',
            'manage automations',
            'manage family',
            'manage behavioral',
        ];

        $userPermissions = $user->permissions->pluck('name')->toArray();

        return view('admin.users.show', [
            'user' => $user,
            'availablePermissions' => $availablePermissions,
            'userPermissions' => $userPermissions,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $newRole = $request->string('role')->toString();

        $currentAdminCount = User::role('admin')->count();
        $isCurrentlyAdmin = $user->hasRole('admin');

        if ($newRole === 'admin' && ! $isCurrentlyAdmin && $currentAdminCount >= 2) {
            return back()->with('error', 'Maksimal 2 admin diperbolehkan.');
        }

        if ($newRole !== 'admin' && $isCurrentlyAdmin && $currentAdminCount <= 1) {
            return back()->with('error', 'Harus ada minimal satu admin aktif.');
        }

        $user->syncRoles([$newRole]);
        $user->save();

        return back()->with('status', 'Akses pengguna diperbarui.');
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $availablePermissions = [
            'access dashboard',
            'manage transactions',
            'manage accounts',
            'manage budgets',
            'manage goals',
            'view reports',
            'manage automations',
            'manage family',
            'manage behavioral',
        ];

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|in:'.implode(',', $availablePermissions),
        ]);

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Hak akses admin tidak dapat diubah di sini.');
        }

        $selected = $request->input('permissions', []);
        $syncedPermissions = array_values(array_intersect($availablePermissions, $selected));

        // Pastikan dashboard selalu ada agar pengalaman tidak rusak
        if (! in_array('access dashboard', $syncedPermissions, true)) {
            $syncedPermissions[] = 'access dashboard';
        }

        $user->syncRoles(['user']);
        $user->syncPermissions($syncedPermissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', 'Hak akses pengguna diperbarui.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'deactivation_message' => 'nullable|string|max:500',
        ]);

        $currentAdminCount = User::role('admin')->count();
        $isCurrentlyAdmin = $user->hasRole('admin');
        $isActivating = $request->boolean('is_active');

        if (! $isActivating && $isCurrentlyAdmin && $currentAdminCount <= 1) {
            return back()->with('error', 'Harus ada minimal satu admin aktif.');
        }

        $user->is_active = $isActivating;
        $user->deactivation_message = $isActivating ? null : $request->string('deactivation_message')->toString();
        $user->save();

        return back()->with('status', 'Status pengguna diperbarui.');
    }
}
