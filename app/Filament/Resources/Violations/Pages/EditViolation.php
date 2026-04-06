<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditViolation extends EditRecord
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

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Pelanggaran')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pelanggaran ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->modalCancelActionLabel('Batal')
                ->successNotification(null)
                ->after(function () {
                    session()->flash('swal_title', 'Dihapus!');
                    session()->flash('swal_text', 'Data pelanggaran telah berhasil dihapus.');
                    session()->flash('swal_icon', 'success');

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $signature = $data['signature'] ?? null;

        if ($signature && str_starts_with($signature, 'data:image')) {
            $data['use_signature_pad'] = true;
            $data['signature_upload']  = null;
        } else {
            $data['use_signature_pad'] = false;
            $data['signature_upload']  = $signature;
            $data['signature']         = null;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['signature_upload'])) {
            $data['signature'] = $data['signature_upload'];
        }

        unset($data['signature_upload']);
        unset($data['use_signature_pad']);

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        session()->flash('swal_title', 'Berhasil!');
        session()->flash('swal_text', 'Data pelanggaran telah berhasil diperbarui.');
        session()->flash('swal_icon', 'success');

        return $this->getResource()::getUrl('index');
    }
}
