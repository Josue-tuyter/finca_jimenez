<?php

namespace App\Filament\Widgets;

use App\Models\Drying_tracking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TextureVariationsChart extends ChartWidget
{
    protected function getType(): string
    {
        return 'bar'; // Usamos 'bar' para un gráfico de barras
    }
    protected static ?string $heading = 'Variaciones en la Textura';
    protected static ?string $description = 'Visualización mensual de las variaciones en la textura durante el secado';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'small';

    protected function getData(): array
    {
        // Obtenemos datos de textura del último año
        $textures = Drying_tracking::select('textura', DB::raw('count(*) as count'))
            ->groupBy('textura')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Textura',
                    'data' => $textures->pluck('count'),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)', // Suave
                        'rgba(255, 159, 64, 0.2)', // Poco aspera
                        'rgba(255, 205, 86, 0.2)', // Aspera
                        'rgba(75, 192, 192, 0.2)', // Quebradiza
                        'rgba(54, 162, 235, 0.2)', // Pegajosa
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)', // Suave
                        'rgba(255, 159, 64, 1)', // Poco aspera
                        'rgba(255, 205, 86, 1)', // Aspera
                        'rgba(75, 192, 192, 1)', // Quebradiza
                        'rgba(54, 162, 235, 1)', // Pegajosa
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $textures->pluck('textura'),
        ];
    }
}