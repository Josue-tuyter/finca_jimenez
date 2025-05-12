<?php

namespace App\Filament\Resources\HarvestPlanningResource\Pages;

use App\Filament\Resources\HarvestPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHarvestPlanning extends EditRecord
{
    protected static string $resource = HarvestPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
