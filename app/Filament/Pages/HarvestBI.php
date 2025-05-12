<?php

namespace App\Filament\Pages;


use App\Filament\Widgets\HarvestTrackingTypeOverview;
use App\Filament\Widgets\HarvestsChart;

use Filament\Pages\Page;
use Filament\Widgets\Widget;


class HarvestBI extends Page
{
    protected static ?string $navigationGroup = 'Despachos y Gráficos';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Cosecha BI';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.harvest-b-i';
    protected function getWidgets(): array
    {
        return [
            HarvestTrackingTypeOverview::class,
            HarvestsChart::class,
            
        ];
    }

    protected function getViewData(): array
    {
        return [
            'widgets' => $this->getWidgets(),
        ];
    }


    

}


