<?php

namespace App\Filament\Resources\RepoResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class RepoStatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Stars', $this->record->stars_count)
                ->icon('heroicon-m-star'),
            Stat::make('Forks', $this->record->forks_count)
                ->icon('heroicon-m-arrow-path'),
            Stat::make('Open Issues', $this->record->open_issues_count)
                ->icon('heroicon-m-exclamation-circle'),
        ];
    }
}
