<?php

namespace App\Filament\Resources\PrivateGPT\Pages;

use App\Filament\Resources\PrivateGPT\PrivateGPTResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrivateGPTAnalyses extends ListRecords
{
    protected static string $resource = PrivateGPTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}