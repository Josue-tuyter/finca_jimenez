<?php

namespace App\Filament\Widgets;

use App\Models\Fermentation_tracking;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class FermentationsChart extends ChartWidget
{
    protected function getType(): string
    {
        return 'line'; // Cambiamos a 'line' para asegurar compatibilidad
    }
    protected static ?string $heading = 'Estadísticas de Fermentaciones';
    protected static ?string $description = 'Visualización mensual de fermentaciones, peso total, humedad';
    protected static ?int $sort = 2;    
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Obtenemos datos de fermentaciones del último año
        $fermentations = Trend::model(Fermentation_tracking::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->sum('total_weight');

        // $temperatures = Trend::model(Fermentation_tracking::class)
        //     ->between(
        //         start: now()->subYear(),
        //         end: now(),
        //     )
        //     ->perMonth()
        //     ->average('temperature');

        $humidities = Trend::model(Fermentation_tracking::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->average('humidity');

        // $locations = Trend::model(Fermentation_tracking::class)
        //     ->between(
        //         start: now()->subYear(),
        //         end: now(),
        //     )
        //     ->perMonth()
        //     ->count('location_id');

        return [
            'datasets' => [
                [
                    'label' => 'Peso Total de Fermentaciones',
                    'data' => $fermentations->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'fill' => true,
                ],
                // [
                //     'label' => 'Temperatura Promedio',
                //     'data' => $temperatures->map(fn (TrendValue $value) => $value->aggregate),
                //     'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                //     'borderColor' => 'rgba(255, 99, 132, 1)',
                //     'borderWidth' => 1,
                //     'fill' => true,
                // ],
                [
                    'label' => 'Humedad Promedio',
                    'data' => $humidities->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'fill' => true,
                ],
                // [
                //     'label' => 'Cantidad de Fermentaciones por Lugar',
                //     'data' => $locations->map(fn (TrendValue $value) => $value->aggregate),
                //     'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                //     'borderColor' => 'rgba(153, 102, 255, 1)',
                //     'borderWidth' => 1,
                //     'fill' => true,
                // ],
            ],
            'labels' => $fermentations->map(fn (TrendValue $value) => $value->date),
        ];
    }
}