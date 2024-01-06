<?php

namespace App\Filament\Resources\LiveCallResource\Pages;

use App\Filament\Resources\LiveCallResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLiveCall extends EditRecord
{
    protected static string $resource = LiveCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
