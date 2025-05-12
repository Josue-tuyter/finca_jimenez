<?php

namespace App\Filament\Resources\FermentationPlanningResource\Pages;

use App\Filament\Resources\FermentationPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFermentationPlanning extends EditRecord
{
    protected static string $resource = FermentationPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
