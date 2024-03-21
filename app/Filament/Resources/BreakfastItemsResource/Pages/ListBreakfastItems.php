<?php

namespace App\Filament\Resources\BreakfastItemsResource\Pages;

use App\Filament\Resources\BreakfastItemsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBreakfastItems extends ListRecords
{
    protected static string $resource = BreakfastItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
