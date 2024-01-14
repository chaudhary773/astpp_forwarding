<?php

namespace App\Filament\Resources\LiveCallResource\Pages;

use App\Filament\Resources\LiveCallResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLiveCalls extends ListRecords
{
    protected static string $resource = LiveCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           LiveCallResource\Widgets\StatsOverview::class
        ];
    }
}
