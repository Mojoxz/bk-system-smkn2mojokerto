<?php

namespace App\Filament\Resources\ViolationTypes\Pages;

use App\Filament\Resources\ViolationTypes\ViolationTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListViolationTypes extends ListRecords
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
