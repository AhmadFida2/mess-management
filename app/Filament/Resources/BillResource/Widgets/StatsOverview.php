<?php

namespace App\Filament\Resources\BillResource\Widgets;

use App\Models\Bill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Bills', function (){
                return Bill::all()->count();
            }) ->description('Total Bills in database')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('primary'),
            Card::make('Paid Bills', function (){
                return DB::table('bills')->where('status','=','2')->count();
            })->description('All Paid Bills')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Card::make('Unpaid Bills', function (){
                return DB::table('bills')->where('status','<','2')->count();
            })->description('Pending Bills')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
