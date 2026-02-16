<?php

namespace App\Filament\Widgets;

use App\Models\Violation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentViolationsTable extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Violation::query()
                    ->with(['student', 'violationType.category'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('violation_date')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.class')
                    ->label('Kelas'),

                Tables\Columns\TextColumn::make('violationType.category.code')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('violationType.name')
                    ->label('Jenis Pelanggaran')
                    ->limit(30),

                Tables\Columns\TextColumn::make('points')
                    ->label('Poin')
                    ->badge()
                    ->color(fn ($state) => $state >= 50 ? 'danger' : ($state >= 25 ? 'warning' : 'success')),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    }),
            ]);
    }

    protected static ?string $heading = 'Pelanggaran Terbaru';
}
