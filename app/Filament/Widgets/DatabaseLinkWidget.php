<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DatabaseLinkWidget extends Widget
{
    protected static string $view = 'filament.widgets.database-link-widget';
    
    protected int | string | array $columnSpan = 'full'; // Esto controla el ancho del widget
    protected int | string | array $columnSpanTablet = 1; // Esto controla el ancho del widget en tabletas


    protected static ?int $sort = 1; // Esto controla el orden del widget
}
