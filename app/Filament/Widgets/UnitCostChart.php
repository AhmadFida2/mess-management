<?php

namespace App\Filament\Widgets;

use App\Models\UnitCost;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UnitCostChart extends ChartWidget
{

    protected static ?string $heading = 'Chart';

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()->is_admin;
    }

    protected function getData(): array
    {
        $data = Trend::model(UnitCost::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfMonth(),
            )
            ->dateColumn('month')
            ->perMonth()
            ->average('cost');
        return [
            'datasets' => [
                [
                    'label' => 'Unit Cost',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
            'borderColor'=> 'rgb(75, 192, 192)',
            'tension'=> 0.1
        ];
    }
    public function getHeading(): string
    {
        return 'Historical Unit Cost';
    }
}
