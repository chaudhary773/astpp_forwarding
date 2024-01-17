<?php

namespace App\Filament\Resources\TargetResource\Pages;

use App\Filament\Resources\TargetResource;
use App\Models\Campaign;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    protected static ?string $breadcrumb = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['campaignname'] = Campaign::find($data['campaign_id'])->camp_name;
        $data['customerid'] = auth()->id();
        $data['creationdate'] = now();
        $data['calltype'] = '1';
        $data['callcontrol'] = '1';
        return $data;
    }
}
