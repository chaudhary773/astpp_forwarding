<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\CDR;
use App\Models\DailyCdr;
use App\Models\Did;
use App\Models\LiveCall;
use App\Models\Target;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    public function getColumns(): int
    {
        return 4;
    }
    protected function getStats(): array
    {
        $liveCall = LiveCall::where('customerid', auth()->id());
        $totalCampaigns = Campaign::where('customer_id', auth()->id());
        $totalDids = Did::allDids();
        $totalTargets = Target::allTargets();
        $todayMissed = DailyCdr::where('customerid', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->where('missed', 1)
            ->count('buyernumber');
        $todayAnswered = DailyCdr::where('customerid', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->where('missed', 0)
            ->count('buyernumber');

        $month = now()->format('Y-m');
        $monthlyAnswered = CDR::where('customerid', auth()->id())
            ->where('month', $month)
            ->where('missed', 0)
            ->count('forwardednumber');

        $monthlyMissed = CDR::where('customerid', auth()->id())
            ->where('month', $month)
            ->where('missed', 1)
            ->count('forwardednumber');

        return [
            Stat::make('Total Campaigns', $totalCampaigns->count()),
            Stat::make('Total DIDs', $totalDids->count()),
            Stat::make('Total Targets', $totalTargets->count()),
            Stat::make('Total Live Calls', $liveCall->count()),
            Stat::make('Today Answered Calls', $todayAnswered),
            Stat::make('Today Missed Calls', $todayMissed),
            Stat::make('Month Answered Calls', $monthlyAnswered),
            Stat::make('Month Missed Calls', $monthlyMissed),
        ];
    }

}
