<?php

namespace App\Filament\Github\CommitResource\Pages;

use App\Filament\Github\Resources\CommitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommit extends CreateRecord
{
    protected static string $resource = CommitResource::class;
}
