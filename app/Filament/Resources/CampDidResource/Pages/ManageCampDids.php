<?php

namespace App\Filament\Resources\CampDidResource\Pages;

use App\Filament\Resources\CampDidResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCampDids extends ManageRecords
{
    protected static string $resource = CampDidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
