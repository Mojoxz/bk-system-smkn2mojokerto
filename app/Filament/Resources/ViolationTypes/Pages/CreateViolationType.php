<?php

namespace App\Filament\Resources\ViolationTypeResource\Pages;

use App\Filament\Resources\ViolationTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateViolationType extends CreateRecord
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
