<?php

namespace App\Filament\Resources\LiveCallResource\Pages;

use App\Filament\Resources\LiveCallResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListLiveCalls extends ListRecords
{
    protected static string $resource = LiveCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Action::make('forceRefresh')
            ->label('Reload'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           LiveCallResource\Widgets\StatsOverview::class
        ];
    }
}
