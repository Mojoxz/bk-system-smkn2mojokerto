<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Siswa')
                    ->schema([
                        TextEntry::make('nisn')->label('NISN'),
                        TextEntry::make('name')->label('Nama'),
                        TextEntry::make('class')->label('Kelas'),
                        TextEntry::make('absen')->label('Absen'),
                        TextEntry::make('phone')->label('Telepon'),
                        TextEntry::make('username')->label('Username'),
                        TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Status Akun')
                    ->schema([
                        IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Diupdate Pada')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(3),

                Section::make('Statistik Pelanggaran')
                    ->schema([
                        TextEntry::make('total_points')
                            ->label('Total Poin')
                            ->badge()
                            ->color(fn ($state) => $state >= 100 ? 'danger' : ($state >= 50 ? 'warning' : 'success')),

                        TextEntry::make('violations_count')
                            ->label('Total Pelanggaran')
                            ->state(fn ($record) => $record->violations()->count()),

                        TextEntry::make('approved_violations_count')
                            ->label('Pelanggaran Disetujui')
                            ->state(fn ($record) => $record->violations()->where('status', 'approved')->count()),

                        TextEntry::make('pending_violations_count')
                            ->label('Pelanggaran Pending')
                            ->state(fn ($record) => $record->violations()->where('status', 'pending')->count()),
                    ])
                    ->columns(4),
            ]);
    }
}
