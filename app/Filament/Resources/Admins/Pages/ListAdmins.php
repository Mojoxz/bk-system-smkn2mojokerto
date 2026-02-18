<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Admin')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        if (session()->has('swal_title')) {
            $title = session()->pull('swal_title');
            $text  = session()->pull('swal_text');
            $icon  = session()->pull('swal_icon');

            $this->js("
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                script.onload = function() {
                    Swal.fire({
                        title: '$title',
                        text: '$text',
                        icon: '$icon',
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                };
                document.head.appendChild(script);
            ");
        }
    }

    // Hook ini dipanggil Filament setelah record dihapus dari table
    public function deleteAction(): \Filament\Actions\Action
    {
        return parent::deleteAction()
            ->requiresConfirmation()
            ->modalHeading('Hapus Admin')
            ->modalDescription('Apakah Anda yakin ingin menghapus admin ini? Tindakan ini tidak dapat dibatalkan.')
            ->modalSubmitActionLabel('Ya, Hapus')
            ->modalCancelActionLabel('Batal')
            ->successNotification(null)
            ->after(function () {
                $this->js("
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                    script.onload = function() {
                        Swal.fire({
                            title: 'Dihapus!',
                            text: 'Admin telah berhasil dihapus.',
                            icon: 'success',
                            confirmButtonColor: '#4f46e5',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    };
                    document.head.appendChild(script);
                ");
            });
    }
}
