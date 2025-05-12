<?php

namespace App\Filament\Widgets;

use App\Models\Drying_tracking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ColorVariationsChart extends ChartWidget
{
    protected static ?string $heading = 'Variaciones en el Color';
    protected static ?string $description = 'Visualización mensual de las variaciones en el color durante el secado';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'small';

    protected function getType(): string
    {
        return 'bar'; // Tipo de gráfico por defecto
    }

    protected function getData(): array
    {
        // Obtenemos datos de color del último año
        $colors = Drying_tracking::select('color', DB::raw('count(*) as count'))
            ->groupBy('color')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Color',
                    'data' => $colors->pluck('count'),
                    'backgroundColor' => [
                        'rgba(123, 63, 0, 0.2)', //  chocolate
                        'rgba(128, 128, 128, 0.2)', // gris
                        'rgba(181, 101, 29, 0.2)', // naron claro
                        'rgba(101, 67, 33, 0.2)', //   marron oscuro
                        'rgba(128, 0, 128, 0.2)', //   purpura
                    ],
                    'borderColor' => [
                        'rgba(92, 47, 0, 1)', //  chocolate
                        'rgba((96, 96, 96, 1)', // gris
                        'rgba(153, 85, 17, 1)', //  maron claro
                        'rgba(85, 50, 20, 1)', //   marron oscuro
                        'rgba(102, 0, 102, 1)', //  purpura
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $colors->pluck('color'),
        ];
    }


}