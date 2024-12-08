<?php

namespace App\Providers\Filament;

use App\Filament\Github\Pages\GithubDashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class GithubPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('github')
            ->path('github')
            ->login()
            ->brandName('GitHub Integration')
            ->colors([
                'primary' => Color::Orange,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
            ])
            ->discoverResources(in: app_path('Filament/Github/Resources'), for: 'App\\Filament\\Github\\Resources')
            ->discoverPages(in: app_path('Filament/Github/Pages'), for: 'App\\Filament\\Github\\Pages')
            ->pages([
                GithubDashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin Panel')
                    ->icon('heroicon-o-cog')
                    ->url('/admin')
                    ->visible(fn () => auth()->user()?->can('view_admin_panel')),
            ])
            ->databaseNotifications()
            ->plugin(FilamentShieldPlugin::make())
            ->renderHook(
                name: PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                hook: fn () => $this->renderLoginLink()
            );
    }

    protected function renderLoginLink(): ?string
    {
        if (app()->environment('local')) {
            return Blade::render('<x-login-link />');
        }

        return null;
    }
}
