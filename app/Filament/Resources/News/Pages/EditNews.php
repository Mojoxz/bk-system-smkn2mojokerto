<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditNews extends EditRecord
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

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Berita')
                ->modalDescription('Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->modalCancelActionLabel('Batal')
                ->successNotification(null)
                ->after(function () {
                    session()->flash('swal_title', 'Dihapus!');
                    session()->flash('swal_text', 'Berita telah berhasil dihapus.');
                    session()->flash('swal_icon', 'success');

                    $this->redirect($this->getResource()::getUrl('index'));
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
        session()->flash('swal_text', 'Data berita telah berhasil diperbarui.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
