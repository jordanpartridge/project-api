<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'view_admin_panel']);
        Permission::create(['name' => 'view_github_panel']);

        // Create roles
        Role::create(['name' => 'super_admin'])
            ->givePermissionTo(Permission::all());
    }

    /** @test */
    public function super_admin_can_access_all_panels(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        $response = $this->get('/admin');
        $response->assertStatus(200);

        $response = $this->get('/github');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_permissions_cannot_access_panels(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin');
        $response->assertStatus(403);

        $response = $this->get('/github');
        $response->assertStatus(403);
    }

    /** @test */
    public function user_with_specific_permission_can_access_only_authorized_panel(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_admin_panel');

        $this->actingAs($user);

        $response = $this->get('/admin');
        $response->assertStatus(200);

        $response = $this->get('/github');
        $response->assertStatus(403);
    }
}
