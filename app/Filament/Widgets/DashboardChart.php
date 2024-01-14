<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\CDR;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DashboardChart extends ChartWidget
{
    protected static ?string $heading = 'Total Monthly Calls';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '250px';
    protected static ?string $pollingInterval = '20s';
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

   // public ?string $filter = 'month';

    protected function getData(): array
    {
     //   $activeFilter = $this->filter;
        $allCalls = Trend::query(CDR::where('customerid', auth()->id()))
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfDay(),
            )
            ->dateColumn('call_start')
            ->perDay()
//            ->perMonth()
//            ->perYear()
            ->count();
        $answeredCalls = Trend::query(CDR::where('customerid', auth()->id())->where('missed', 0))
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfDay(),
            )
            ->dateColumn('call_start')
            ->perDay()
//            ->perMonth()
//            ->perYear()
            ->count();
        $missedCalls = Trend::query(CDR::where('customerid', auth()->id())->where('missed', 1))
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfDay(),
            )
            ->dateColumn('call_start')
            ->perDay()
//            ->perMonth()
//            ->perYear()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'All Calls',
                    'data' => $allCalls->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' =>'#f59e0b',
                    'borderColor' => '#f59e0b',
                    'color' => '#f59e0b',

                ],
                [
                    'label' => 'Answered Call',
                    'data' => $answeredCalls->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' =>'#155724',
                    'borderColor' => '#155724',
                    'color' => '#155724',
                ],
                [
                    'label' => 'Missed Call',
                    'data' => $missedCalls->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#E5097F',
                    'borderColor' => '#E5097F',
                    'color' => '#E5097F',
                ],
            ],
            'labels' => $allCalls->map(fn (TrendValue $value) => $value->date),
           // 'activeFilter' => $activeFilter,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
