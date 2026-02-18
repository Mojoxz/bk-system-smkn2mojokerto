<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditAdmin extends EditRecord
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

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Admin')
                ->modalDescription('Apakah Anda yakin ingin menghapus admin ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->modalCancelActionLabel('Batal')
                ->successNotification(null)
                ->successRedirectUrl($this->getResource()::getUrl('index'))
                ->after(function () {
                    session()->flash('swal_title', 'Dihapus!');
                    session()->flash('swal_text', 'Admin telah berhasil dihapus.');
                    session()->flash('swal_icon', 'success');
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        session()->flash('swal_title', 'Berhasil!');
        session()->flash('swal_text', 'Data admin telah berhasil diperbarui.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
