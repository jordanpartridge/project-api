<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Spatie\LoginLink\LoginLink;
use Tests\DuskTestCase;

class FilamentLoginTest extends DuskTestCase
{
    /**
     * Test Filament login page accessibility
     *
     * @return void
     */
    public function test_filament_login_page_is_accessible()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->assertSee('Login')
                    ->assertVisible('input[name="email"]')
                    ->assertVisible('input[name="password"]');
        });
    }

    /**
     * Test successful Filament login with credentials
     *
     * @return void
     */
    public function test_successful_filament_credential_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/admin/login')
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/admin')
                    ->assertAuthenticated();
        });
    }

    /**
     * Test Spatie Login Link functionality
     *
     * @return void
     */
    public function test_spatie_login_link()
    {
        $user = User::factory()->create();

        $loginLink = LoginLink::createFor($user);

        $this->browse(function (Browser $browser) use ($loginLink) {
            $browser->visit($loginLink->url)
                    ->assertPathIs('/admin')
                    ->assertAuthenticated()
                    ->assertSee($user->name);
        });
    }

    /**
     * Test failed login attempt
     *
     * @return void
     */
    public function test_failed_login_attempt()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->type('email', 'nonexistent@example.com')
                    ->type('password', 'wrongpassword')
                    ->press('Login')
                    ->assertPathIs('/admin/login')
                    ->assertSee('These credentials do not match our records');
        });
    }
}
