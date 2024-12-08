<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'view_admin_panel']);
        Permission::create(['name' => 'view_github_panel']);

        // Create roles and assign permissions
        Role::create(['name' => 'super_admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'admin'])
            ->givePermissionTo(['view_admin_panel']);

        Role::create(['name' => 'github_user'])
            ->givePermissionTo(['view_github_panel']);
    }
}
