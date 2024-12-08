<?php

namespace App\Filament\Github\OwnerResource\Pages;

use App\Filament\Github\Resources\OwnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOwner extends CreateRecord
{
    protected static string $resource = OwnerResource::class;
}
