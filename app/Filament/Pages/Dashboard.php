<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AutoAttendances;
use App\Filament\Widgets\LatestAttendances;
use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\LatestPayments;
use App\Filament\Widgets\MenuCard;
use App\Filament\Widgets\MessOverview;
use App\Filament\Widgets\UnitCostChart;
use App\Filament\Widgets\UserOverview;
use App\Models\DailyAttendance;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{


    public function getWidgets(): array
    {
        return [
            UserOverview::class,
            MenuCard::class,
            AutoAttendances::class,
            LatestAttendances::class,
            LatestPayments::class,
            LatestOrders::class,
            MessOverview::class,
            UnitCostChart::class,


        ];
    }

    public function getTitle(): string
    {
        if(auth()->user()->is_admin)
        {
            return "Admin Dashboard";
        }
        elseif(auth()->user()->is_staff)
        {
            return "Staff Dashboard";
        }
        else
        {
            return "User Dashboard";
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Mark Attendance')
                ->action(function (array $data): void {
                    abort_if(Carbon::parse($data['date'])->lt(now()->startOfDay()),401);
                    $mid = auth()->user()->member->id;
                    $check = DailyAttendance::where('date',$data['date'])->where('member_id',$mid)->first();
                    if(!isset($check))
                    {
                        $att = new DailyAttendance([
                            'date' => $data['date'],
                            'is_lunch' => $data['is_lunch']??false,
                            'is_dinner' => $data['is_dinner']??false,
                            'member_id' => $mid,
                        ]);
                        $att->save();
                        Notification::make()
                            ->title('Attendance Marked!')
                            ->success()
                            ->send();
                    }
                    else
                    {
                        if(isset($data['is_lunch']))
                            $check->is_lunch = $data['is_lunch'];
                        $check->is_dinner = $data['is_dinner'];
                        $check->save();
                        Notification::make()
                            ->title('Attendance Updated!')
                            ->success()
                            ->send();
                    }


                })
                ->form([
                    DatePicker::make('date')->format('Y-m-d')->required()->default(function (){
                        $hour = now()->hour;
                        return $hour < 16 ? now() : now()->addDay();
                    })->closeOnDateSelection()->disabled()->afterStateUpdated(function ($state, callable $set){
                        $at = DailyAttendance::where('date',Carbon::parse($state)->format('Y-m-d'))->where('member_id',auth()->user()->member->id)->first();
                        $set('is_lunch', $at->is_lunch??false);
                        $set('is_dinner', $at->is_dinner??false);
                    }),
                    Toggle::make('is_lunch')->required()->default(fn(callable $get)=> DailyAttendance::where('date',Carbon::parse($get('date'))->format('Y-m-d'))->where('member_id',auth()->user()->member->id)->first()->is_lunch??false)
                        ->hidden( fn($get) => Carbon::parse($get('date'))->format('N') != 5)->label('Open Lunch?'),
                    Toggle::make('is_dinner')->required()->default(fn($get)=> DailyAttendance::where('date',Carbon::parse($get('date'))->format('Y-m-d'))->where('member_id',auth()->user()->member->id)->first()->is_dinner??false)->label('Open Dinner?'),
                ])->color('success')->visible(fn() : bool => !auth()->user()->is_admin && isset(auth()->user()->member))


        ];
    }

}
