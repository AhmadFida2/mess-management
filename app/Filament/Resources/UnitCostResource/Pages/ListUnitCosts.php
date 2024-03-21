<?php

namespace App\Filament\Resources\UnitCostResource\Pages;

use App\Filament\Resources\UnitCostResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitCosts extends ListRecords
{
    protected static string $resource = UnitCostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
