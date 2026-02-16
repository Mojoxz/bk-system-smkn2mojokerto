<?php

namespace App\Filament\Resources\ViolationTypeResource\Pages;

use App\Filament\Resources\ViolationTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditViolationType extends EditRecord
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
