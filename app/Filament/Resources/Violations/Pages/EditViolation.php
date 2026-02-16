<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use App\Services\ViolationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditViolation extends EditRecord
{
    protected static string $resource = ViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = new ViolationService();
        return $service->updateViolation($record, $data);
    }
}
