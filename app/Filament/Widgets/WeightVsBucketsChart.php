<?php

namespace App\Filament\Widgets;

use App\Models\Fermentation_tracking;
use App\Models\Harvest_tracking;
use Filament\Widgets\ChartWidget;

class WeightVsBucketsChart extends ChartWidget
{
    protected static ?string $heading = 'Peso Total vs Cantidad de Baldes Comprados';
    protected static ?int $sort = 2;
    

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {

        $data = Fermentation_tracking::orderBy('id')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Peso Total (Lb)',
                    'data' => $data->pluck('weight')->toArray(),
                    'backgroundColor' => '#28a745',
                    'borderWidth' => 0,
                ],
                [
                    'label' => 'Baldes Comprados (Peso en Lb)',
                    'data' => $data->pluck('B_weight')->toArray(),
                    'backgroundColor' => '#ffcc00',
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->pluck('harvest.name')->toArray(),
            'options' => [
                'scales' => [
                    'x' => ['stacked' => true],
                    'y' => ['stacked' => true],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
