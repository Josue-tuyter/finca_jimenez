<?php

namespace App\Filament\Resources\DryingPlanningResource\Pages;

use App\Filament\Resources\DryingPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDryingPlanning extends EditRecord
{
    protected static string $resource = DryingPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
