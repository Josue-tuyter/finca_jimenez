<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\ColorVariationsChart;
use App\Filament\Widgets\Drying_trackingTypeOverview;
use App\Filament\Widgets\DryingsChart;
use App\Filament\Widgets\TextureVariationsChart;
use Filament\Widgets\Widget;

class DryingBI extends Page
{

    protected static ?string $navigationGroup ='Despachos y Gráficos';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Secado BI';
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.pages.drying-b-i';

    protected function getWidgets(): array
    {
        return [
            Drying_trackingTypeOverview::class,
            DryingsChart::class,
            ColorVariationsChart::class,
            TextureVariationsChart::class,
            
        ];
    }

    protected function getViewData(): array
    {
        return [
            'widgets' => $this->getWidgets(),
        ];
    }
}
