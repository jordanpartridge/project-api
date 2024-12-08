<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GithubPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create GitHub-specific permissions if they don't exist
        $permissions = [
            'view_github_panel',
            'manage_github_integrations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Update super_admin role with new permissions
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(Permission::all());
        }

        // Create or update github_admin role
        $githubAdminRole = Role::firstOrCreate(['name' => 'github_admin']);
        $githubAdminRole->syncPermissions([
            'view_github_panel',
            'manage_github_integrations',
        ]);
    }
}
