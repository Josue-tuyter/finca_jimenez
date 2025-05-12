<?php

namespace App\Filament\Resources\ParcelResource\Pages;

use App\Filament\Resources\ParcelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageParcels extends ManageRecords
{
    protected static string $resource = ParcelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
