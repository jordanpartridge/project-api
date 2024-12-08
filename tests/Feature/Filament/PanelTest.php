<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function unauthorized_users_cannot_access_admin_panel()
    {
        $response = $this->get('/admin');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function unauthorized_users_cannot_access_github_panel()
    {
        $response = $this->get('/github');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function authorized_user_can_access_admin_panel()
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function authorized_user_can_access_github_panel()
    {
        $user = User::factory()->create(['can_access_github' => true]);

        $response = $this->actingAs($user)
            ->get('/github');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function panel_navigation_shows_correct_items()
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'can_access_github' => true,
        ]);

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertSee('GitHub Panel');
        $response->assertSee('Admin Panel');
    }

    /**
     * @test
     */
    public function panel_colors_are_correctly_applied()
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'can_access_github' => true,
        ]);

        // Test admin panel
        $response = $this->actingAs($user)
            ->get('/admin');
        $response->assertSee('bg-blue-500');

        // Test github panel
        $response = $this->actingAs($user)
            ->get('/github');
        $response->assertSee('bg-orange-500');
    }
}
