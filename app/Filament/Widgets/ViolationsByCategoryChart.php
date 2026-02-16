<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ViolationsByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Violations By Category Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
