<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary permissions and role
        $adminRole = Role::create(['name' => 'admin']);

        $permissions = [
            'view_admin_panel',
            'view_github_panel',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->syncPermissions($permissions);
    }

    #[Test]
    public function unauthorized_users_cannot_access_admin_panel()
    {
        $response = $this->get('/admin');
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function unauthorized_users_cannot_access_github_panel()
    {
        $response = $this->get('/github');
        $response->assertStatus(302);
        $response->assertRedirect('/github/login');
    }

    #[Test]
    public function authorized_user_can_access_admin_panel()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertSuccessful();
    }

    #[Test]
    public function authorized_user_can_access_github_panel()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)
            ->get('/github');

        $response->assertSuccessful();
    }

    #[Test]
    public function panel_navigation_shows_correct_items()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Check admin panel navigation
        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertSuccessful();
        $response->assertSeeInOrder(['GitHub Panel', 'Admin Panel'], false);

        // Check github panel navigation
        $response = $this->actingAs($user)
            ->get('/github');

        $response->assertSuccessful();
        $response->assertSee('GitHub Integration');
    }

    #[Test]
    public function panels_have_correct_branding()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Check admin panel branding
        $response = $this->actingAs($user)
            ->get('/admin');
        $response->assertSuccessful();
        $response->assertSee(config('app.name'));

        // Check github panel branding
        $response = $this->actingAs($user)
            ->get('/github');
        $response->assertSuccessful();
        $response->assertSee('GitHub Integration');
    }
}
