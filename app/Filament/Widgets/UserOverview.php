<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return !( auth()->user()->is_admin || auth()->user()->is_staff);
    }

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Attendances';

    protected function getStats(): array
    {
        if(auth()->user()->member)
        {
            return [
                Stat::make('Pending Bills', fn ()=> Bill::where('member_id',auth()->user()->member->id)
                    ->where('status','<',2)->count())
                    ->description('Total Pending Bills')
                    ->descriptionIcon('heroicon-s-clipboard')
                    ->color('danger'),
                Stat::make('Account Balance',  fn ()=> 'PKR '. auth()->user()->member->account_balance)
                    ->description('Currrent Running Balance')
                    ->descriptionIcon('heroicon-s-currency-dollar')
                    ->color('primary'),
                Stat::make('Security', fn ()=> 'PKR '. auth()->user()->member->security)
                    ->description('Security Deposit')
                    ->descriptionIcon('heroicon-s-user')
                    ->color('success'),
            ];
        }
        else
            return [
                Stat::make('Welcome' , fn() => 'User')
                    ->description("No Profile attached")
                    ->descriptionIcon('heroicon-s-user')
                    ->color('success'),
            ];

    }
}
