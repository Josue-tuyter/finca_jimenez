<?php

namespace App\Filament\Resources\DryingMethodResource\Pages;

use App\Filament\Resources\DryingMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDryingMethods extends ManageRecords
{
    protected static string $resource = DryingMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
