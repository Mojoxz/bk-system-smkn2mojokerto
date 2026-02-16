<?php

namespace App\Filament\Resources\ViolationTypes\Schemas;

use App\Models\ViolationCategory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ViolationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jenis Pelanggaran')
                    ->schema([
                        Select::make('violation_category_id')
                            ->label('Kategori Pelanggaran')
                            ->options(ViolationCategory::active()->ordered()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama Jenis Pelanggaran')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Terlambat, Tidak Mengerjakan PR, dll'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Poin yang akan ditambahkan jika melakukan pelanggaran ini'),
                        Toggle::make('is_custom')
                            ->label('Custom Poin')
                            ->helperText('Jika diaktifkan, admin bisa input poin sendiri saat mencatat pelanggaran')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
