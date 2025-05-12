<?php

namespace App\Filament\Widgets;

use App\Models\Fermentation_tracking;
use App\Models\Harvest_tracking;
use Filament\Widgets\ChartWidget;

class FermentationLocationChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Fermentación por Lugar';
    protected static ?int $sort = 2;  
    protected int | string | array $columnSpan = 'medium';

    protected function getData(): array
    {
        $data = Fermentation_tracking::selectRaw('location_id, COUNT(*) as total')
            ->groupBy('location_id')
            ->with('location')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                ],
            ],
            'labels' => $data->pluck('location.name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
