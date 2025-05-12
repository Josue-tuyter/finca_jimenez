<?php

namespace App\Filament\Widgets;

use App\Models\Fermentation_tracking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class Fermentation_trackingTypeOverview extends BaseWidget
{
    protected static ?string $heading = 'Estadísticas de Fermentación';
    
    protected static ?int $sort = 2;





    protected function getStats(): array
    {
        // Definir la consulta base para fermentaciones activas
        $baseQuery = Fermentation_tracking::whereHas('fermentation_planning', function ($query) {
            $query->where('F_date_start', '<=', now())
                ->where('F_date_end', '>=', now());
        });

        // Calcular estadísticas básicas
        $totalWeight = $baseQuery->sum('total_weight');
        $averageWeight = $baseQuery->avg('total_weight');
        $totalTracking = $baseQuery->count();
        $totalBucketWeight = $baseQuery->sum('B_weight');

        // Consulta para humedad más común
        $humidityDistribution = DB::table('fermentation_trackings')
            ->select('humidity', DB::raw('COUNT(*) as count'))
            ->whereIn('id', $baseQuery->pluck('id'))
            ->groupBy('humidity')
            ->orderByDesc('count')
            ->first();

        // Consulta para temperatura más común
        $temperatureDistribution = DB::table('fermentation_trackings')
            ->select('temperature', DB::raw('COUNT(*) as count'))
            ->whereIn('id', $baseQuery->pluck('id'))
            ->groupBy('temperature')
            ->orderByDesc('count')
            ->first();

        // Consulta para ubicación más común
        $mostCommonLocation = DB::table('fermentation_trackings')
            ->select('location_id', DB::raw('COUNT(*) as count'))
            ->whereIn('id', $baseQuery->pluck('id'))
            ->groupBy('location_id')
            ->orderByDesc('count')
            ->first();

        $locationName = null;
        if ($mostCommonLocation) {
            $locationName = DB::table('locations')
                ->where('id', $mostCommonLocation->location_id)
                ->value('name');
        }

        return [
            // **Título para Seguimiento General**
            Stat::make('Fermentación', '🔍 Seguimiento General de la Fermentación')
                ->description('Estadísticas generales del proceso de fermentación')
                ->color('gray'),

            Stat::make('Total de Seguimientos', $totalTracking)
                ->description('Número total de registros')
                ->color('success'),

            Stat::make('Peso Total en Fermentación', number_format($totalWeight, 2) . ' Lb')
                ->description('Suma de todos los pesos')
                ->color('success'),

            Stat::make('Peso Promedio', number_format($averageWeight, 2) . ' Lb')
                ->description('Promedio por seguimiento')
                ->color('info'),

            // **Título para Condiciones de Proceso**
            Stat::make('Título', '🌡️ Condiciones de Proceso')
                ->description('Humedad y temperatura predominantes')
                ->color('gray'),

            Stat::make('Condición de Humedad más Común', $humidityDistribution ? $humidityDistribution->humidity : 'N/A')
                ->description('Nivel de humedad más frecuente')
                ->color('warning'),

            Stat::make('Temperatura más Común', $temperatureDistribution ? $temperatureDistribution->temperature : 'N/A')
                ->description('Nivel de temperatura más frecuente')
                ->color('warning'),

            // **Título para Ubicaciones y Baldes**
            Stat::make('Título', '📍 Ubicación y Baldes')
                ->description('Datos relacionados con ubicación y peso de baldes')
                ->color('gray'),

            Stat::make('Total Peso de Baldes', number_format($totalBucketWeight, 2) . ' Lb')
                ->description('Peso total de baldes comprados')
                ->color('info'),

            Stat::make(
                'Ubicación más Utilizada',
                $locationName ?? 'N/A'
            )
                ->description(
                    $mostCommonLocation 
                        ? $mostCommonLocation->count . ' fermentaciones' 
                        : ''
                )
                ->color('success'),
        ];
    }
}