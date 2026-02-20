<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassroomResource\Pages;
use App\Models\Classroom;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-rectangle-group';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kelas';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Kelas';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Kelas')
                    ->schema([
                        Select::make('major_id')
                            ->label('Jurusan')
                            ->relationship('major', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('grade')
                            ->label('Tingkat')
                            ->options([
                                'X'   => 'X',
                                'XI'  => 'XI',
                                'XII' => 'XII',
                            ])
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Kelas')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('XI RPL 1'),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Tingkat')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('major.name')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('major.code')
                    ->label('Kode')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name'),
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Tingkat')
                    ->options([
                        'X'   => 'X',
                        'XI'  => 'XI',
                        'XII' => 'XII',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index'  => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit'   => Pages\EditClassroom::route('/{record}/edit'),
        ];
    }
}
