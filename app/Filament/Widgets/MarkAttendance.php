<?php

namespace App\Filament\Widgets;

use App\Models\DailyAttendance;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MarkAttendance extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->is_admin || auth()->user()->is_staff;
    }

    protected function getTableQuery(): Builder
    {
        return DailyAttendance::query()->where('date',now()->format('Y-m-d'));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('member.name'),
            TextColumn::make('is_lunch')->label('Lunch Open')->formatStateUsing(function($state,$record)
            {
                if($record->is_lunch_taken)
                    return "Yes (Taken)";
                else
                return $state?"Yes":"No";
            }),
            TextColumn::make('is_dinner')->label('Dinner Open')->formatStateUsing(function($state,$record)
            {
                if($record->is_dinner_taken)
                    return "Yes (Taken)";
                else
                    return $state?"Yes":"No";
            }),
            Tables\Columns\IconColumn::make('is_lunch_taken')->boolean(),
            Tables\Columns\IconColumn::make('is_dinner_taken')->boolean(),

        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('lunch_taken')
                ->action(function (Model $record){
                    $record->is_lunch_taken = true;
                    $record->save();
                })
                ->visible(fn(Model $record) => $record->is_lunch && !$record->is_lunch_taken),
            Action::make('dinner_taken')->action(function (Model $record){
                $record->is_dinner_taken = true;
                $record->save();
            })
                ->visible(fn(Model $record) => $record->is_dinner && !$record->is_dinner_taken),
        ];
    }
}
