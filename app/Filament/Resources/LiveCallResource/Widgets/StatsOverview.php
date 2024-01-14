<?php

namespace App\Filament\Resources\LiveCallResource\Widgets;

use App\Models\LiveCall;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $liveCall = LiveCall::where('customerid', auth()->id());
        return [
            Stat::make('Live Calls', $liveCall->count()),
        ];
    }
}
