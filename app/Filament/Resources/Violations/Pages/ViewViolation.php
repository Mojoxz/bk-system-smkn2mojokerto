<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewViolation extends ViewRecord
{
    protected static string $resource = ViolationResource::class;

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
                        TextEntry::make('student.nisn')->label('NISN'),
                        TextEntry::make('student.name')->label('Nama'),
                        TextEntry::make('student.class')->label('Kelas'),
                    ])
                    ->columns(3),

                Section::make('Data Pelanggaran')
                    ->schema([
                        TextEntry::make('violationType.category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('violationType.name')->label('Jenis Pelanggaran'),
                        TextEntry::make('points')
                            ->label('Poin')
                            ->badge()
                            ->color(fn ($state) => $state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success')),
                        TextEntry::make('violation_date')
                            ->label('Tanggal Pelanggaran')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Bukti')
                    ->schema([
                        ImageEntry::make('photo_evidence')
                            ->label('Foto Bukti')
                            ->visible(fn ($record) => $record->photo_evidence),
                        ImageEntry::make('signature')
                            ->label('Tanda Tangan')
                            ->visible(fn ($record) => $record->signature),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->photo_evidence || $record->signature),

                Section::make('Status')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            }),
                        TextEntry::make('admin.name')->label('Dicatat Oleh'),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->notes),
                    ])
                    ->columns(2),

                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Diupdate Pada')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
