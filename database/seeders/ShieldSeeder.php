<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create base permissions
        $permissions = [
            'view_admin_panel',
            'view_github_panel',
            'manage_github_integrations',
            'manage_permissions',
            // Resource permissions
            'view_any_project',
            'view_project',
            'create_project',
            'update_project',
            'delete_project',
            'restore_project',
            'force_delete_project',
            // User permissions
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $roles = [
            'super_admin' => $permissions,
            'admin' => [
                'view_admin_panel',
                'view_any_project',
                'view_project',
                'create_project',
                'update_project',
                'delete_project',
                'view_any_user',
                'view_user',
            ],
            'github_user' => [
                'view_github_panel',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
