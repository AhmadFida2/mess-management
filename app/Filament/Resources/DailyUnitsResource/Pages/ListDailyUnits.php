<?php

namespace App\Filament\Resources\DailyUnitsResource\Pages;

use App\Filament\Resources\DailyUnitsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyUnits extends ListRecords
{
    protected static string $resource = DailyUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
