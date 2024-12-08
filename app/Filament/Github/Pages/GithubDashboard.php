<?php

namespace App\Filament\Github\Pages;

use Filament\Pages\Page;

class GithubDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationLabel = 'GitHub Dashboard';
    protected static ?string $title = 'GitHub Dashboard';
    protected static ?string $slug = 'dashboard';
    protected static ?int $navigationSort = -2;

    protected static string $view = 'filament.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
