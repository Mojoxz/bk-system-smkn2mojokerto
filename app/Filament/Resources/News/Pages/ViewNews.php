<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;

class ViewNews extends ViewRecord
{
    protected static string $resource = NewsResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Berita')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul'),
                        TextEntry::make('slug')
                            ->label('Slug'),
                        TextEntry::make('admin.name')
                            ->label('Penulis'),
                        ImageEntry::make('image')
                            ->label('Gambar')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->label('Konten')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Status Publikasi')
                    ->schema([
                        IconEntry::make('is_published')
                            ->label('Dipublikasi')
                            ->boolean(),
                        TextEntry::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('views')
                            ->label('Dilihat')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
            ]);
    }
}
