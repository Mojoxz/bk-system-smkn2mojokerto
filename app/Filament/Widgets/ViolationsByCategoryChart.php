<?php

namespace App\Filament\Widgets;

use App\Models\Violation;
use App\Models\ViolationCategory;
use Filament\Widgets\ChartWidget;

class ViolationsByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Pelanggaran per Kategori';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $categories = ViolationCategory::with(['violationTypes.violations' => function ($query) {
            $query->where('status', 'approved');
        }])->get();

        $data = [];
        $labels = [];

        foreach ($categories as $category) {
            $labels[] = $category->code . ' - ' . $category->name;
            $count = 0;

            foreach ($category->violationTypes as $type) {
                $count += $type->violations->count();
            }

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pelanggaran',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
