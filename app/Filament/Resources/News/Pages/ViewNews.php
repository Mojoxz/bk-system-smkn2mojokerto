<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewNews extends ViewRecord
{
    protected static string $resource = NewsResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Berita')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')->label('Judul'),
                        Infolists\Components\TextEntry::make('slug')->label('Slug'),
                        Infolists\Components\TextEntry::make('admin.name')->label('Penulis'),
                        Infolists\Components\ImageEntry::make('image')
                            ->label('Gambar')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('content')
                            ->label('Konten')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Status Publikasi')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_published')
                            ->label('Dipublikasi')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('views')
                            ->label('Dilihat')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
            ]);
    }
}
