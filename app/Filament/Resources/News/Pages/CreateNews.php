<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

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

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        session()->flash('swal_title', 'Berhasil!');
        session()->flash('swal_text', 'Berita baru telah berhasil ditambahkan.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
