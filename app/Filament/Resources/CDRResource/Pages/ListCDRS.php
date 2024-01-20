<?php

namespace App\Filament\Resources\CDRResource\Pages;

use App\Filament\Exports\CDRExporter;
use App\Filament\Resources\CDRResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCDRS extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = CDRResource::class;


    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(CDRExporter::class)
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Calls'),
            'answered' => Tab::make('Answered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('missed', false)),
            'missed' => Tab::make('Missed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('missed', true)),
        ];
    }
}
