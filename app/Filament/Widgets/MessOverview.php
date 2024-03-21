<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use App\Models\Member;
use App\Models\Payment;
use App\Models\UnitCost;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MessOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->is_admin;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Members', fn () => Member::all()->count())
                ->description('Total Mess Members')
                ->descriptionIcon('heroicon-s-user')
                ->color('success'),
            Stat::make('Bills', fn ()=> 'Rs. ' . Bill::all()->sum('amount'))
                ->description('Total Amount of Bills')
                ->descriptionIcon('heroicon-s-clipboard')
                ->color('warning'),
            Stat::make('Payments',  fn ()=> 'Rs. ' . Payment::all()->sum('amount'))
                ->description('Total Amount of Payments')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('danger'),
            Stat::make('Unit Cost',  function (){
                    $uc = UnitCost::query()->latest('month')->first();
                if($uc==null) return 0;
                else
                    return $uc->cost;
            } )
                ->description('Last Month Unit Cost')
                ->descriptionIcon('heroicon-s-calculator')
                ->color('primary'),
            Stat::make('Pending Bills',  fn ()=> Bill::where('status','<>','2')->count())
                ->description('Bills with Pending Payment')
                ->descriptionIcon('heroicon-s-clock')
                ->color('danger'),
            Stat::make('Mess Securities',  fn ()=> Member::all()->sum('security'))
                ->description('Total Amount of Securities')
                ->descriptionIcon('heroicon-s-clock')
                ->color('warning'),
        ];
    }
}
