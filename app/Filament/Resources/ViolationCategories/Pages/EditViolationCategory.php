<?php

namespace App\Filament\Resources\ViolationCategoryResource\Pages;

use App\Filament\Resources\ViolationCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditViolationCategory extends EditRecord
{
    protected static string $resource = ViolationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
