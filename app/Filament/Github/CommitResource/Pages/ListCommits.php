<?php

namespace App\Filament\Github\CommitResource\Pages;

use App\Filament\Github\Resources\CommitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommits extends ListRecords
{
    protected static string $resource = CommitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
