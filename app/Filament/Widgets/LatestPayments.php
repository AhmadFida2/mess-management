<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPayments extends BaseWidget
{
    public static function canView(): bool
    {
        return !(auth()->user()->is_admin) && isset(auth()->user()->member);
    }

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Payments';

    protected function getTableQuery(): Builder
    {
        return Payment::query()->where('member_id',auth()->user()->member->id)->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('payment_method.name')->label('Payment Method'),
            TextColumn::make('amount')->label('Amount'),
            TextColumn::make('bill.bill_details')->label('Bill Details'),

        ];
    }
}
