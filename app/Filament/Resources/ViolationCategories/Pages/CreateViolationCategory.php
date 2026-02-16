<?php

namespace App\Filament\Resources\ViolationCategoryResource\Pages;

use App\Filament\Resources\ViolationCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateViolationCategory extends CreateRecord
{
    protected static string $resource = ViolationCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
