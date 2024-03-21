<?php

namespace App\Filament\Widgets;

use App\Models\Menu;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MenuCard extends BaseWidget
{
    public static function canView(): bool
    {
        return !(auth()->user()->is_admin);
    }

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getCards(): array
    {
        return [
            Stat::make('Today\'s Breakfast'  , function(){
                $menu = Menu::where('date',now()->format('Y-m-d'))->latest()->first();
                $string ='';
                if(!isset($menu))
                {
                    return  'No Menu Set';
                }
                else
                {
                    return $menu->breakfast ?? "Usual";
                }
            })
                ->description("Date: ". now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('primary'),
            Stat::make('Today\'s Lunch'  , function(){
                $menu = Menu::where('date',now()->format('Y-m-d'))->latest()->first();
                $string ='';
                if(!isset($menu))
                {
                    return 'No Menu Set';
                }
                else
                {
                    return $menu->lunch ?? "No Lunch";
                }
            })
                ->description("Date: ". now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),
            Stat::make('Today\'s Dinner'  , function(){
                $menu = Menu::where('date',now()->format('Y-m-d'))->latest()->first();
                $string ='';
                if(!isset($menu))
                {
                    return 'No Menu Set';
                }
                else
                {
                   return $menu->dinner;
                }
            })
                ->description("Date: ". now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('danger'),


        ];
    }
}
