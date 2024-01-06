<?php

namespace App\Filament\Resources\TargetResource\Pages;

use App\Filament\Resources\TargetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customerid'] = auth()->id();
        $data['creationdate'] = now();
        $data['calltype'] = '1';
        $data['callcontrol'] = '1';
        return $data;
    }
}
