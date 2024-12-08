<?php

namespace App\Filament\Github\Resources\RepoResource\Pages;

use App\Filament\Github\Resources\RepoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRepo extends CreateRecord
{
    protected static string $resource = RepoResource::class;
}
