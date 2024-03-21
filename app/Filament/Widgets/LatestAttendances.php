<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Closure;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestAttendances extends BaseWidget
{
    public static function canView(): bool
    {
        return !(auth()->user()->is_admin) && isset(auth()->user()->member);
    }

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Manual Attendances';

    protected function getTableQuery(): Builder
    {
        return Attendance::query()->where('member_id',auth()->user()->member->id)->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('date')->formatStateUsing(fn($state) => Carbon::parse($state)->format('d M Y')),
            TextColumn::make('meal'),
            TextColumn::make('units'),
        ];
    }
}
