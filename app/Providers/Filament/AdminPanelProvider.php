<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(config('app.name'))
            ->colors($this->getPanelColors())
            ->topNavigation($this->getTopNavigationConfig($panel))
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'User Management',
                'Settings',
            ])
            ->maxContentWidth('7xl')
            ->resources($this->getResourceConfig())
            ->pages($this->getPageConfig())
            ->widgets($this->getWidgetConfig())
            ->middleware($this->getMiddleware())
            ->authMiddleware([Authenticate::class])
            ->renderHook(
                name: PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                hook: fn () => $this->renderLoginLink()
            );
    }

    protected function getPanelColors(): array
    {
        return [
            'primary' => Color::Sky,    // Electric blue that pops in both modes
            'gray' => Color::Zinc,   // Clean neutral base
            'danger' => Color::Rose,   // Sharp contrast for alerts
            'success' => Color::Teal,   // Crisp tech-green feedback
        ];
    }

    protected function getTopNavigationConfig(Panel $panel): callable
    {
        return fn (): \Illuminate\View\View => view('panels.topbar', [
            'currentPanel' => $panel->getId(),
            'backgroundColor' => match ($panel->getId()) {
                'admin' => 'bg-blue-500',
                'support' => 'bg-green-500',
                default => 'bg-gray-500',
            },
        ]);
    }

    protected function getResourceConfig(): array
    {
        return [
            'discover' => [
                'in' => app_path('Filament/Resources'),
                'for' => 'App\\Filament\\Resources',
            ],
        ];
    }

    protected function getPageConfig(): array
    {
        return [
            'discover' => [
                'in' => app_path('Filament/Pages'),
                'for' => 'App\\Filament\\Pages',
            ],
            'register' => [
                Dashboard::class,
            ],
        ];
    }

    protected function getWidgetConfig(): array
    {
        return [
            'discover' => [
                'in' => app_path('Filament/Widgets'),
                'for' => 'App\\Filament\\Widgets',
            ],
            'register' => [
                AccountWidget::class,
            ],
        ];
    }

    protected function getMiddleware(): array
    {
        return [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];
    }

    protected function renderLoginLink(): ?string
    {
        if (config('app.env') === 'local') {
            return Blade::render('<x-login-link />');
        }

        return null;
    }
}
