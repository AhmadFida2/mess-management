<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LatestOrders extends BaseWidget
{

    public static function canView(): bool
    {
        return !(auth()->user()->is_admin) && isset(auth()->user()->member);
    }

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Bills';

    protected function getTableQuery(): Builder
    {
        return Bill::query()->where('member_id',auth()->user()->member->id)->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('month')->label('Bill Month')->date('F Y'),
            TextColumn::make('units')->label('Units Consumed'),
            TextColumn::make('amount')->label('Bill Amount'),
            TextColumn::make('Paid Amount')
                ->getStateUsing(function($record) {
                    return DB::Table('payments')->where('bill_id','=',$record->id)->sum('amount');
                }),
            TextColumn::make('status')->label('Status')->getStateUsing(function ($record) {
                $stats = ['Un-Paid','Partial Paid','Paid'];
                return $stats[$record->status];
            })
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('print')->label('Print Bill')
                ->url(fn (Bill $record): string => route('bill-print', $record->id))
                ->openUrlInNewTab()
                ->color('success')
                ->icon('heroicon-o-printer')
                ->tooltip('Print this Bill to A4 Format')
        ];
    }

}
