<?php

namespace App\Filament\Widgets;

use App\Models\Harvest_tracking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class HarvestTrackingTypeOverview extends BaseWidget
{
    protected static ?string $heading = 'Estadísticas de Cosecha';
    
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calcular el tamaño promedio
        $averageSize = number_format(
            Harvest_tracking::avg('size'),
            2
        );

        // Contar el total de registros
        $totalTracking = Harvest_tracking::count();

        // Distribución de humedad
        $humidityDistribution = Harvest_tracking::select('humidity', DB::raw('count(*) as total'))
            ->groupBy('humidity')
            ->orderByDesc('total')
            ->first();

        $mostCommonHumidity = $humidityDistribution ? $humidityDistribution->humidity : 'N/A';

        // Distribución de enfermedades
        $diseaseCount = Harvest_tracking::where('disease', '!=', 'Ninguna')->count();
        $diseasePercentage = $totalTracking > 0 
            ? number_format(($diseaseCount / $totalTracking) * 100, 1) 
            : 0;

        // Obtener la enfermedad más común (excluyendo 'Ninguna')
        $mostCommonDisease = Harvest_tracking::select('disease', DB::raw('count(*) as total'))
            ->where('disease', '!=', 'Ninguna')
            ->groupBy('disease')
            ->orderByDesc('total')
            ->first();

        return [
            // **Título para Seguimiento General**
            Stat::make('Cosecha', '🔍 Seguimiento General de Cosecha')
                ->description('Estadísticas generales del proceso de cosecha')
                ->color('gray'),

            Stat::make('Total de Seguimientos', $totalTracking)
                ->description('Número total de registros de cosecha')
                ->color('success'),

            Stat::make('Tamaño Promedio', $averageSize)
                ->description('Promedio de tamaño por registro de cosecha')
                ->color('info'),

            // **Título para Condiciones de Proceso**
            Stat::make('Título', '🌿 Condiciones de Cosecha')
                ->description('Niveles predominantes de humedad')
                ->color('gray'),

            Stat::make('Nivel de Humedad más Común', $mostCommonHumidity)
                ->description('Condición de humedad más frecuente en la cosecha')
                ->color('warning'),

            // **Título para Estado de Salud**
            Stat::make('Título', '🩺 Estado de Salud de la Cosecha')
                ->description('Distribución de enfermedades en los registros')
                ->color('gray'),

            Stat::make('Porcentaje con Enfermedades', $diseasePercentage . '%')
                ->description($diseaseCount . ' registros con enfermedades')
                ->color('danger'),

            Stat::make(
                'Enfermedad más Común',
                $mostCommonDisease 
                    ? $mostCommonDisease->disease 
                    : 'Sin enfermedades registradas'
            )
                ->description(
                    $mostCommonDisease 
                        ? $mostCommonDisease->total . ' casos registrados' 
                        : ''
                )
                ->color('danger'),
        ];
    }
}
