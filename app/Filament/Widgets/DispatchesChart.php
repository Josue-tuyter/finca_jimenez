<?php

namespace App\Filament\Widgets;

use App\Models\Dispatch;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DispatchesChart extends ChartWidget
{
    protected static ?string $heading = 'Estadísticas de Despachos';
    protected static ?string $description = 'Visualización mensual de despachos y cantidad de sacos';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function getDescription(): ?string
    {
        return 'Seguimiento mensual de despachos realizados';
    }

    protected function getData(): array
    {
        // Obtener datos de despachos del último año
        $dispatches = Trend::model(Dispatch::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // Obtener el total de sacos por mes
        $sacks = Trend::model(Dispatch::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->sum('number_sacks');

            return [
                'datasets' => [
                    [
                        'label' => 'Número de Despachos',
                        'data' => $dispatches->pluck('aggregate')->toArray(),
                        'borderColor' => '#36A2EB',
                        'backgroundColor' => '#36A2EB',
                        'fill' => false,
                    ],
                    [
                        'label' => 'Total de Sacos',
                        'data' => $sacks->pluck('aggregate')->toArray(),
                        'borderColor' => '#FF6384',
                        'backgroundColor' => '#FF6384',
                        'fill' => false,
                        'yAxisID' => 'y1',
                    ]
                ],
                'labels' => $dispatches->pluck('date')->toArray(),
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
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Número de Despachos'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Total de Sacos'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
