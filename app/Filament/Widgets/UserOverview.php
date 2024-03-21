<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MenuResource;
use App\Models\Bill;
use App\Models\Member;
use App\Models\Menu;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class UserOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return !( auth()->user()->is_admin || auth()->user()->is_staff);
    }

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Attendances';

    protected function getCards(): array
    {
        if(auth()->user()->member)
        {
            return [
                Card::make('Pending Bills', fn ()=> Bill::where('member_id',auth()->user()->member->id)
                    ->where('status','<',2)->count())
                    ->description('Total Pending Bills')
                    ->descriptionIcon('heroicon-s-clipboard')
                    ->color('danger'),
                Card::make('Account Balance',  fn ()=> 'PKR '. auth()->user()->member->account_balance)
                    ->description('Currrent Running Balance')
                    ->descriptionIcon('heroicon-s-currency-dollar')
                    ->color('primary'),
                Card::make('Security', fn ()=> 'PKR '. auth()->user()->member->security)
                    ->description('Security Deposit')
                    ->descriptionIcon('heroicon-s-user')
                    ->color('success'),
            ];
        }
        else
            return [
                Card::make('Welcome' , fn() => 'User')
                    ->description("No Profile attached")
                    ->descriptionIcon('heroicon-s-user')
                    ->color('success'),
            ];

    }
}
