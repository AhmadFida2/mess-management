<?php

namespace App\Filament\Resources\BillResource\Widgets;

use App\Models\Bill;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use HtmlSanitizer\Extension\Table\Node\TrNode;

class BlogPostsChart extends LineChartWidget
{
    protected static ?string $heading = 'Billing History Chart';

    protected function getData(): array
    {
        $data = Trend::query(Bill::where('member_id', '=', auth()->user()->id))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->dateColumn('month')
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Billing History',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
