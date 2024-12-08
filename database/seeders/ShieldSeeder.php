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
            // Panel Access
            'view_admin_panel',
            'view_github_panel',

            // Project Permissions
            'view_any_project',
            'view_project',
            'create_project',
            'update_project',
            'delete_project',
            'restore_project',
            'force_delete_project',

            // GitHub Integration
            'manage_github_integrations',
            'sync_repositories',
            'manage_webhooks',

            // User Management
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',

            // Activity Log
            'view_activity_log',

            // Shield/Permissions Management
            'manage_permissions',
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
                'view_activity_log',
            ],
            'github_user' => [
                'view_github_panel',
                'manage_github_integrations',
                'sync_repositories',
                'manage_webhooks',
                'view_any_project',
                'view_project',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
