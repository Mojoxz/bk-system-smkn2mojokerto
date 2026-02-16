<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Violation;
use App\Models\Admin;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Siswa', Student::count())
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Total Pelanggaran', Violation::count())
                ->description(Violation::where('status', 'pending')->count() . ' menunggu persetujuan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Pelanggaran Bulan Ini', Violation::whereMonth('created_at', now()->month)->count())
                ->description('Dari ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('danger'),

            Stat::make('Total Admin', Admin::count())
                ->description('Admin aktif: ' . Admin::where('is_active', true)->count())
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
