<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Fermentation_trackingTypeOverview;
use App\Filament\Widgets\FermentationLocationChart;
use App\Filament\Widgets\FermentationsChart;
use App\Filament\Widgets\TemperatureHumidityChart;
use App\Filament\Widgets\WeightVsBucketsChart;
use Filament\Pages\Page;
use Filament\Widgets\Widget;

class FermentationBI extends Page
{

    protected static ?string $navigationGroup = 'Despachos y Gráficos';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Fermentación BI';
    protected static string $view = 'filament.pages.fermentation-b-i';

    protected function getWidgets(): array
    {
        return [
            Fermentation_trackingTypeOverview::class,
            FermentationsChart::class,
            FermentationLocationChart::class,
            TemperatureHumidityChart::class,
            WeightVsBucketsChart::class,

            
        ];
    }

    protected function getViewData(): array
    {
        return [
            'widgets' => $this->getWidgets(),
        ];
    }

}
