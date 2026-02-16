<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Schemas\Schema;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\TextEntry::make('nisn')->label('NISN'),
                    Infolists\Components\TextEntry::make('name')->label('Nama'),
                    Infolists\Components\TextEntry::make('class')->label('Kelas'),
                    Infolists\Components\TextEntry::make('absen')->label('Absen'),
                    Infolists\Components\TextEntry::make('phone')->label('Telepon'),
                    Infolists\Components\TextEntry::make('username')->label('Username'),
                    Infolists\Components\TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                ])
                    ->columns(2),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('total_points')
                            ->label('Total Poin')
                            ->badge()
                            ->color(fn ($state) => $state >= 100 ? 'danger' : ($state >= 50 ? 'warning' : 'success')),

                        Infolists\Components\TextEntry::make('violations_count')
                            ->label('Total Pelanggaran')
                            ->state(fn ($record) => $record->violations()->count()),

                        Infolists\Components\TextEntry::make('approved_violations_count')
                            ->label('Pelanggaran Disetujui')
                            ->state(fn ($record) => $record->violations()->where('status', 'approved')->count()),

                        Infolists\Components\TextEntry::make('pending_violations_count')
                            ->label('Pelanggaran Pending')
                            ->state(fn ($record) => $record->violations()->where('status', 'pending')->count()),
                    ])
                    ->columns(4),
            ]);
    }
}
