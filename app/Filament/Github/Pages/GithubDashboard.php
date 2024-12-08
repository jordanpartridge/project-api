<?php

namespace App\Filament\Github\Pages;

use Filament\Pages\Dashboard;

class GithubDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static string $view = 'filament.pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return 'GitHub Dashboard';
    }
}
