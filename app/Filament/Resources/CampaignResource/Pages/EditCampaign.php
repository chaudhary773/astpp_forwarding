<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
//            Actions\Action::make('add_target')
//                ->action(function (): void {
//                    $result = $this->process(static fn (Model $record) => $record->delete());
//                }),
            Actions\DeleteAction::make(),
        ];
    }


}
