<?php

namespace App\Filament\Github\FileResource\Pages;

use App\Filament\Github\Resources\FileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFile extends CreateRecord
{
    protected static string $resource = FileResource::class;
}
