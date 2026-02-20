<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make([
                    TextEntry::make('nisn')->label('NISN'),
                    TextEntry::make('name')->label('Nama'),
                    TextEntry::make('classroom.name')->label('Kelas'),
                    TextEntry::make('classroom.major.name')->label('Jurusan'),
                    TextEntry::make('absen')->label('Absen'),
                    TextEntry::make('phone')->label('Telepon'),
                    TextEntry::make('username')->label('Username'),
                    TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                ])
                    ->columns(2),

                Group::make()
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
