<?php

namespace App\Filament\Widgets;

use App\Models\Documentation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentationStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Documents', Documentation::count())
                ->description('Total documentation articles')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart(Documentation::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('count')
                    ->toArray()
                ),

            Stat::make('Published Documents', Documentation::published()->count())
                ->description('Public documentation')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),

            Stat::make('Draft Documents', Documentation::where('is_published', false)->count())
                ->description('Unpublished drafts')
                ->descriptionIcon('heroicon-m-pencil')
                ->color('warning'),
        ];
    }
}
