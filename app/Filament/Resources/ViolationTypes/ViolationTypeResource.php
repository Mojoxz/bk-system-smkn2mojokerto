<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViolationTypeResource\Pages;
use App\Models\ViolationType;
use App\Models\ViolationCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ViolationTypeResource extends Resource
{
    protected static ?string $model = ViolationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Jenis Pelanggaran';

    protected static ?string $navigationGroup = 'Pengelolaan Pelanggaran';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('violation_category_id')
                            ->label('Kategori Pelanggaran')
                            ->options(ViolationCategory::active()->ordered()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Jenis Pelanggaran')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Terlambat, Tidak Mengerjakan PR, dll'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Poin yang akan ditambahkan jika melakukan pelanggaran ini'),
                        Forms\Components\Toggle::make('is_custom')
                            ->label('Custom Poin')
                            ->helperText('Jika diaktifkan, admin bisa input poin sendiri saat mencatat pelanggaran')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.code')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Jenis Pelanggaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Poin')
                    ->badge()
                    ->color(fn ($state) => $state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_custom')
                    ->label('Custom')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('violations_count')
                    ->label('Jumlah Kasus')
                    ->counts('violations')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('violation_category_id')
                    ->label('Kategori')
                    ->options(ViolationCategory::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                Tables\Filters\TernaryFilter::make('is_custom')
                    ->label('Custom Poin')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListViolationTypes::route('/'),
            'create' => Pages\CreateViolationType::route('/create'),
            'edit' => Pages\EditViolationType::route('/{record}/edit'),
        ];
    }
}
