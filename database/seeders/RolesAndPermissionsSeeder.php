<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'access dashboard',
            'manage users',
            'manage transactions',
            'manage accounts',
            'manage budgets',
            'manage goals',
            'view reports',
            'manage automations',
            'manage family',
            'manage behavioral',
            'manage education',
            'manage news',
            'access admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $adminRole->givePermissionTo($permissions);

        $userRole->syncPermissions([
            'access dashboard',
            'manage transactions',
            'manage accounts',
            'manage budgets',
            'manage goals',
        ]);
    }
}
