<?php

namespace App\Filament\Resources\RepoResource\Pages;

use App\Filament\Resources\RepoResource;
use App\Filament\Resources\RepoResource\Widgets\RepoStatsOverview;
use App\Models\Repo;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRepo extends ViewRecord
{
    protected static string $resource = RepoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            EditAction::make(),
            Action::make('viewOnGithub')
                ->label('Open in GitHub')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->button()
                ->size('sm')
                ->outlined()
                ->extraAttributes([
                    'class' => 'flex items-center gap-1 border-gray-700 bg-gray-800 text-gray-200 hover:bg-gray-700',
                ])
                ->url(fn (Repo $record) => $record->url)
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RepoStatsOverview::class,
        ];
    }
}
