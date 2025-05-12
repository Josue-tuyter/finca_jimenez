<?php

namespace App\Filament\Widgets;

use App\Models\Fermentation_tracking;
use Filament\Widgets\ChartWidget;

class TemperatureHumidityChart extends ChartWidget
{
    protected static ?string $heading = 'Evolución de Temperatura y Humedad';
    protected static ?int $sort = 2;  

    protected function getData(): array
    {
        $data = Fermentation_tracking::with('harvest')->orderBy('id')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Temperatura (°C)',
                    'data' => $data->pluck('temperature')->map(fn($val) => (float) $val)->toArray(),
                    'borderColor' => '#ff5733',
                    'backgroundColor' => 'rgba(255, 87, 51, 0.5)',
                ],
                [
                    'label' => 'Humedad (%)',
                    'data' => $data->pluck('humidity')->map(fn($val) => (float) $val)->toArray(),
                    'borderColor' => '#3399ff',
                    'backgroundColor' => 'rgba(51, 153, 255, 0.5)',
                ],
            ],
            'labels' => $data->pluck('harvest.name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
