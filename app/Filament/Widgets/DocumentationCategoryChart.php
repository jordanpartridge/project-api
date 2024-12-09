<?php

namespace App\Filament\Widgets;

use App\Models\Documentation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DocumentationCategoryChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Documentation by Category';

    protected static string $color = 'info';

    protected function getData(): array
    {
        $data = Documentation::query()
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Documents',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#10B981', // Emerald
                        '#3B82F6', // Blue
                        '#6366F1', // Indigo
                        '#8B5CF6', // Violet
                    ],
                ],
            ],
            'labels' => $data->pluck('category')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
