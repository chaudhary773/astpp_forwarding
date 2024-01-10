<?php

namespace App\Filament\Resources\CallBlockResource\Pages;

use App\Filament\Resources\CallBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCallBlocks extends ManageRecords
{
    protected static string $resource = CallBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth()->id();
        return $data;
    }
}
