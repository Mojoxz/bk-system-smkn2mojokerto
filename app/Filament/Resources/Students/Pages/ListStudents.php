<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Siswa')
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


}
