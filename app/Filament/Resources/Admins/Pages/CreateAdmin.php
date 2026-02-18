<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

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

    protected function getRedirectUrl(): string
    {
        // Simpan session di sini, tepat sebelum redirect dijalankan
        session()->flash('swal_title', 'Berhasil!');
        session()->flash('swal_text', 'Admin baru telah berhasil ditambahkan.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
