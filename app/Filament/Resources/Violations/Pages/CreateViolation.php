<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateViolation extends CreateRecord
{
    protected static string $resource = ViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();

        if (!empty($data['signature_upload'])) {
            $data['signature'] = $data['signature_upload'];
        }

        unset($data['signature_upload']);
        unset($data['use_signature_pad']);

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        session()->flash('swal_title', 'Berhasil!');
        session()->flash('swal_text', 'Data pelanggaran baru telah berhasil ditambahkan.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
