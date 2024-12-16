<?php

use Spatie\LoginLink\LoginLink;
use App\Models\User;

describe('Filament Authentication', function () {
    it('can render login page', function () {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Login');
    });

    it('authenticates valid user via credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    });

    it('creates and uses login link', function () {
        $user = User::factory()->create();

        $loginLink = LoginLink::createFor($user);

        $this->get($loginLink->url)
            ->assertRedirect('/admin')
            ->assertAuthenticated();
    });

    it('handles invalid login credentials', function () {
        $response = $this->post('/admin/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    });
});
