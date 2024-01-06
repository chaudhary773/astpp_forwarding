<?php

namespace App\Filament\Resources\CDRResource\Pages;

use App\Filament\Resources\CDRResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCDRS extends ListRecords
{
    protected static string $resource = CDRResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
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
