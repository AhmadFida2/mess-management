<?php

namespace App\Filament\Resources\DailyUnitsResource\Pages;

use App\Filament\Resources\DailyUnitsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyUnits extends EditRecord
{
    protected static string $resource = DailyUnitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
