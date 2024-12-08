<?php

namespace App\Providers\Filament;

use App\Filament\Github\Pages\GithubDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
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
            ->favicon('images/github-favicon.png')
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Orange,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Inter')
            ->discoverResources(in: app_path('Filament/Github/Resources'), for: 'App\\Filament\\Github\\Resources')
            ->discoverPages(in: app_path('Filament/Github/Pages'), for: 'App\\Filament\\Github\\Pages')
            ->pages([
                GithubDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Github/Widgets'), for: 'App\\Filament\\Github\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->databaseNotifications()
            ->topNavigation()
            ->maxContentWidth('full')
            ->navigationGroups([
                'Repositories',
                'Integration',
                'Configuration',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::top-navigation',
                fn (): \Illuminate\View\View => view('panels.topbar', [
                    'currentPanel' => $panel->getId(),
                    'backgroundColor' => 'bg-orange-500',
                ])
            )
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin Panel')
                    ->icon('heroicon-o-building-office')
                    ->url('/admin'),
                // Add more menu items as needed
            ]);
    }
}
