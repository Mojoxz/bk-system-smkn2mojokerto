<?php

namespace App\Filament\Resources\ViolationCategoryResource\Pages;

use App\Filament\Resources\ViolationCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListViolationCategories extends ListRecords
{
    protected static string $resource = ViolationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
