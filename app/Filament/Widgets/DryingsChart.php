<?php

namespace App\Filament\Widgets;

use App\Models\Drying_tracking;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class DryingsChart extends ChartWidget
{
    protected function getType(): string
    {
        return 'bar'; // Usamos 'bar' para un gráfico de barras apiladas
    }
    protected static ?string $heading = 'Estadísticas de Secado';
    protected static ?string $description = 'Visualización mensual de secado, humedad, color, textura y moho';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'middle';

    protected function getData(): array
    {
        // Obtenemos datos de secado del último año
        $dryings = Trend::model(Drying_tracking::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count('id');

        $humidities = Drying_tracking::select('humidity', DB::raw('count(*) as count'))
            ->groupBy('humidity')
            ->get();

        $colors = Drying_tracking::select('color', DB::raw('count(*) as count'))
            ->groupBy('color')
            ->get();

        $textures = Drying_tracking::select('textura', DB::raw('count(*) as count'))
            ->groupBy('textura')
            ->get();

        // $molds = Drying_tracking::select('moho', DB::raw('count(*) as count'))
        //     ->groupBy('moho')
        //     ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Humedad',
                    'data' => $humidities->pluck('count'),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Color',
                    'data' => $colors->pluck('count'),
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Textura',
                    'data' => $textures->pluck('count'),
                    'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                    'borderWidth' => 1,
                ],
                // [
                //     'label' => 'Moho',
                //     'data' => $molds->pluck('count'),
                //     'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                //     'borderColor' => 'rgba(54, 162, 235, 1)',
                //     'borderWidth' => 1,
                // ],
            ],
            'labels' => $dryings->map(fn (TrendValue $value) => $value->date),
        ];
    }
}