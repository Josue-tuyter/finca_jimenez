<?php

namespace App\Filament\Widgets;

use App\Models\Harvest;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;


class HarvestsChart extends ChartWidget
{
    protected static ?string $heading = 'Estadísticas de Cosechas';
    protected static ?string $description = 'Visualización mensual de cosechas y peso total';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public function getChartData(): array
    {
        return $this->getData();
    }

    protected function getData(): array
    {
        // Obtenemos datos de cosechas del último año
        $harvests = Trend::model(Harvest::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // Obtenemos el peso total mensual
        $weights = Trend::model(Harvest::class)
        ->between(
            start: now()->subYear(),
            end: now(),
        )
        ->perMonth()
        ->sum('weight');

        return [
            'datasets' => [
                [
                    'label' => 'Número de Cosechas',
                    'data' => $harvests->pluck('aggregate')->toArray(),
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => '#36A2EB',
                    'fill' => false,
                ],
                [
                    'label' => 'Peso Total (Lb)',
                    'data' => $weights->pluck('aggregate')->toArray(),
                    'borderColor' => '#FF6384',
                    'backgroundColor' => '#FF6384',
                    'fill' => false,
                    'yAxisID' => 'y1',
                ]
            ],
            'labels' => $harvests->pluck('date')->toArray(),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }
}