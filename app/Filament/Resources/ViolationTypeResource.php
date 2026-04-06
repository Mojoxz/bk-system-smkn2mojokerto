<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViolationTypeResource\Pages;
use App\Models\ViolationType;
use App\Models\ViolationCategory;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ViolationTypeResource extends Resource
{
    protected static ?string $model = ViolationType::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jenis Pelanggaran';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengelolaan Pelanggaran';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
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
                        Toggle::make('is_custom')
                            ->label('Custom Poin')
                            ->helperText('Aktifkan jika poin tidak kelipatan 5 — admin input nilai manual saat mencatat pelanggaran')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(
                                fn(Set $set, $state) => $state
                                    ? $set('points', null)
                                    : $set('points', 5)
                            ),
                        Select::make('points')
                            ->label('Poin')
                            ->options(
                                collect(range(1, 60))
                                    ->mapWithKeys(fn($i) => [$i * 5 => $i * 5])
                                    ->toArray()
                            )
                            ->required()
                            ->default(5)
                            ->searchable()
                            ->helperText('Pilih poin kelipatan 5 (5–300).')
                            ->hidden(fn(Get $get) => (bool) $get('is_custom'))
                            ->dehydrated(fn(Get $get) => !(bool) $get('is_custom')),
                        TextInput::make('points')
                            ->label('Poin Custom')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(999)
                            ->placeholder('Masukkan poin ganjil, contoh: 7, 13, 27...')
                            ->helperText('Isi poin secara manual untuk nilai yang tidak kelipatan 5.')
                            ->hidden(fn(Get $get) => !(bool) $get('is_custom'))
                            ->dehydrated(fn(Get $get) => (bool) $get('is_custom')),
                        Toggle::make('is_active')
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
                    ->formatStateUsing(fn($state, $record) => $record->is_custom ? 'Custom' : $state)
                    ->color(fn($state, $record) => $record->is_custom
                        ? 'gray'
                        : ($state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success')))
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
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Jenis Pelanggaran')
                    ->modalDescription('Apakah Anda yakin ingin menghapus jenis pelanggaran ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotification(null)
                    ->after(function ($livewire) {
                        $livewire->js("
                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                            script.onload = function() {
                                Swal.fire({
                                    title: 'Dihapus!',
                                    text: 'Jenis pelanggaran telah berhasil dihapus.',
                                    icon: 'success',
                                    confirmButtonColor: '#4f46e5',
                                    confirmButtonText: 'OK',
                                    timer: 3000,
                                    timerProgressBar: true,
                                });
                            };
                            document.head.appendChild(script);
                        ");
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
