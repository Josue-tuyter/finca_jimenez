<?php

namespace App\Filament\Widgets;

use App\Models\Drying_tracking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class Drying_trackingTypeOverview extends BaseWidget
{
    protected static ?string $heading = 'Estadísticas de Secado';
    
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        // Total de registros de secado
        $totalDryingRecords = Drying_tracking::count();

        // Método de secado más común
        $mostCommonDryingMethod = Drying_tracking::select('drying_method_id', DB::raw('count(*) as total'))
            ->groupBy('drying_method_id')
            ->orderByDesc('total')
            ->first();

        $commonDryingMethod = $mostCommonDryingMethod 
            ? $mostCommonDryingMethod->drying_method->name 
            : 'N/A';

        // Humedad más común
        $mostCommonHumidity = Drying_tracking::select('humidity', DB::raw('count(*) as total'))
            ->groupBy('humidity')
            ->orderByDesc('total')
            ->first();

        $commonHumidity = $mostCommonHumidity 
            ? $mostCommonHumidity->humidity 
            : 'N/A';

        // Color más común
        $mostCommonColor = Drying_tracking::select('color', DB::raw('count(*) as total'))
            ->groupBy('color')
            ->orderByDesc('total')
            ->first();

        $commonColor = $mostCommonColor 
            ? $mostCommonColor->color 
            : 'N/A';

        // Porcentaje con moho
        $totalWithMold = Drying_tracking::where('moho', 'Con moho')->count();
        $moldPercentage = $totalDryingRecords > 0 
            ? number_format(($totalWithMold / $totalDryingRecords) * 100, 1) 
            : 0;

        // Textura más común
        $mostCommonTexture = Drying_tracking::select('textura', DB::raw('count(*) as total'))
            ->groupBy('textura')
            ->orderByDesc('total')
            ->first();

        $commonTexture = $mostCommonTexture 
            ? $mostCommonTexture->textura 
            : 'N/A';


        

        return [
            // Título General
            Stat::make('Secado', '🔍 Seguimiento General del Secado')
                ->description('Resumen de estadísticas del proceso de secado')
                ->color('gray'),

            // Estadísticas Clave
            Stat::make('Total de Registros', $totalDryingRecords)
                ->description('Cantidad total de secados registrados')
                ->color('success'),

            Stat::make('Método de Secado más Común', $commonDryingMethod)
                ->description('Método predominante usado en el secado')
                ->color('info'),

            Stat::make('Humedad más Común', $commonHumidity)
                ->description('Nivel de humedad predominante')
                ->color('warning'),

            Stat::make('Color más Común', $commonColor)
                ->description('Color predominante del cacao secado')
                ->color('warning'),

            Stat::make('Porcentaje con Moho', $moldPercentage . '%')
                ->description($totalWithMold . ' registros con moho detectado')
                ->color('danger'),

            Stat::make('Textura más Común', $commonTexture)
                ->description('Textura predominante del cacao')
                ->color('info'),





            
        ];
    }
}
