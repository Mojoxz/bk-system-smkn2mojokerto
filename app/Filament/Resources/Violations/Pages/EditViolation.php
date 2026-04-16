<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

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
        // Handle photo_evidence — kosongkan kalau file tidak ada agar tidak loading terus
        if (!empty($data['photo_evidence'])) {
            $photoExists = Storage::disk('public')->exists($data['photo_evidence']);
            if (!$photoExists) {
                $data['photo_evidence'] = null;
            }
        }

        // Handle signature
        $signature = $data['signature'] ?? null;

        if ($signature && str_starts_with($signature, 'data:image')) {
            // Signature dari SignaturePad (base64) — langsung pakai
            $data['use_signature_pad'] = true;
            $data['signature_upload']  = null;

        } elseif ($signature) {
            // Signature dari FileUpload (path file) — cek apakah file ada
            $fileExists = Storage::disk('public')->exists($signature);
            $data['use_signature_pad'] = false;
            $data['signature_upload']  = $fileExists ? $signature : null;
            $data['signature']         = $fileExists ? null : $signature;

        } else {
            // Tidak ada signature sama sekali
            $data['use_signature_pad'] = true;
            $data['signature_upload']  = null;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $useSignaturePad = $data['use_signature_pad'] ?? true;

        if (!$useSignaturePad) {
            // Mode upload foto
            if (!empty($data['signature_upload'])) {
                // Ada file baru atau file lama yang di-load ulang
                $data['signature'] = $data['signature_upload'];
            }
            // Kalau signature_upload kosong, biarkan signature tidak berubah
            // (tidak di-overwrite dengan null)
        }
        // Kalau mode pad, nilai signature sudah terisi langsung dari SignaturePad

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
