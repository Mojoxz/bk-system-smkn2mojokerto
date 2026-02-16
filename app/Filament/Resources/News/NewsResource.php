<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-newspaper';
    }

    public static function getNavigationLabel(): string
    {
        return 'Berita';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Konten Website';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Informasi Berita')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Berita')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL friendly version of title'),

                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Berita')
                            ->image()
                            ->directory('news')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Konten Berita')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('news/attachments'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Publikasi')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(false)
                            ->live(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->native(false)
                            ->visible(fn (callable $get) => $get('is_published'))
                            ->default(now()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Dipublikasi')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('views')
                    ->label('Dilihat')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua')
                    ->trueLabel('Dipublikasi')
                    ->falseLabel('Draft'),

                Tables\Filters\Filter::make('published_date')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('published_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['published_from'], fn ($q) => $q->whereDate('published_at', '>=', $data['published_from']))
                            ->when($data['published_until'], fn ($q) => $q->whereDate('published_at', '<=', $data['published_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
            'view' => Pages\ViewNews::route('/{record}'),
        ];
    }
}
