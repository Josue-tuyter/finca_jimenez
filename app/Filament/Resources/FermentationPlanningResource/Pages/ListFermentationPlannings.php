<?php

namespace App\Filament\Resources\FermentationPlanningResource\Pages;

use App\Filament\Resources\FermentationPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFermentationPlannings extends ListRecords
{
    protected static string $resource = FermentationPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
