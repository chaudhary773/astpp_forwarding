<?php

namespace App\Filament\Resources\CallBlockResource\Pages;

use App\Filament\Resources\CallBlockResource;
use App\Models\CallBlock;
use App\Models\Campaign;
use Closure;
use Filament\Actions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\NoReturn;

class ManageCallBlocks extends ManageRecords
{
    protected static string $resource = CallBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->model(CallBlock::Class)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['customer_id'] = auth()->id();
                    $data['variations'][] = ltrim($data['number'], 1);
                    $data['variations'][] = $data['number'];
                    $data['variations'][] = "+".$data['number'];
                    return $data;
                     }
                )
                ->using(function (array $data, string $model): Model {
                    $models = [];
                    foreach ($data['variations'] as $variation) {
                        $models[] = CallBlock::create([
                            'number' => $variation,
                            'description' => $data['description'],
                            'status' => $data['status'],
                            'customer_id' => $data['customer_id'],
                        ]);
                    }
                    return $models[0];
                })
            ->form([
                    TextInput::make('number')
                        ->label('Number')
                        ->required()
                        ->unique()
                        ->tel(),
                    TextInput::make('description')
                        ->label('Description')
                        ->required()
                        ->maxLength(200),
                    Toggle::make('status')
                        ->label('Status')
                        ->default(1)
                        ->required(),
            ])
        ];
    }

}
