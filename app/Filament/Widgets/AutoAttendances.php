<?php

namespace App\Filament\Widgets;

use App\Models\DailyAttendance;
use Carbon\Carbon;
use Closure;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AutoAttendances extends BaseWidget
{
    public static function canView(): bool
    {
        return !(auth()->user()->is_admin) && isset(auth()->user()->member);
    }

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Auto Attendances';


    protected function getTableQuery(): Builder
    {
        return DailyAttendance::query()->where('member_id',auth()->user()->member->id)->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('date')->formatStateUsing(fn($state) => Carbon::parse($state)->format('d M Y')),
            TextColumn::make('is_lunch')->label('Lunch Open')->formatStateUsing(fn($state) => $state?"Yes":"No"),
            TextColumn::make('is_dinner')->label('Dinner Open')->formatStateUsing(fn($state) => $state?"Yes":"No"),
        //    TextColumn::make('is_lunch_taken')->label('Lunch Taken')->formatStateUsing(fn($state) => $state?"Yes":"No"),
          //  TextColumn::make('is_dinner_taken')->label('Dinner Taken')->formatStateUsing(fn($state) => $state?"Yes":"No"),


        ];
    }
}
