<?php

namespace App\Filament\Resources\ViolationResource\Pages;

use App\Filament\Resources\ViolationResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewViolation extends ViewRecord
{
    protected static string $resource = ViolationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Siswa')
                    ->schema([
                        Infolists\Components\TextEntry::make('student.nisn')->label('NISN'),
                        Infolists\Components\TextEntry::make('student.name')->label('Nama'),
                        Infolists\Components\TextEntry::make('student.class')->label('Kelas'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Data Pelanggaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('violationType.category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('violationType.name')->label('Jenis Pelanggaran'),
                        Infolists\Components\TextEntry::make('points')
                            ->label('Poin')
                            ->badge()
                            ->color(fn ($state) => $state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success')),
                        Infolists\Components\TextEntry::make('violation_date')
                            ->label('Tanggal Pelanggaran')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Bukti')
                    ->schema([
                        Infolists\Components\ImageEntry::make('photo_evidence')
                            ->label('Foto Bukti')
                            ->visible(fn ($record) => $record->photo_evidence),
                        Infolists\Components\ImageEntry::make('signature')
                            ->label('Tanda Tangan')
                            ->visible(fn ($record) => $record->signature),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->photo_evidence || $record->signature),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
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
                        Infolists\Components\TextEntry::make('admin.name')->label('Dicatat Oleh'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->notes),
                    ])
                    ->columns(2),
            ]);
    }
}
