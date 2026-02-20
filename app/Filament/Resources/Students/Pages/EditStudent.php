<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

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
                ->modalHeading('Hapus Siswa')
                ->modalDescription('Apakah Anda yakin ingin menghapus siswa ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->modalCancelActionLabel('Batal')
                ->successNotification(null)
                ->successRedirectUrl($this->getResource()::getUrl('index'))
                ->after(function () {
                    session()->flash('swal_title', 'Dihapus!');
                    session()->flash('swal_text', 'Siswa telah berhasil dihapus.');
                    session()->flash('swal_icon', 'success');
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['major_id'] = $this->record->classroom?->major_id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        session()->flash('swal_title', 'Berhasil!');
        session()->flash('swal_text', 'Data siswa telah berhasil diperbarui.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
