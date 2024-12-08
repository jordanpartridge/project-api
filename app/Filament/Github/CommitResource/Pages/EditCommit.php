<?php

namespace App\Filament\Github\CommitResource\Pages;

use App\Filament\Github\Resources\CommitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommit extends EditRecord
{
    protected static string $resource = CommitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
