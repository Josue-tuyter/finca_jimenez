<?php

namespace App\Filament\Resources\DryingPlanningResource\Pages;

use App\Filament\Resources\DryingPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDryingPlannings extends ListRecords
{
    protected static string $resource = DryingPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
