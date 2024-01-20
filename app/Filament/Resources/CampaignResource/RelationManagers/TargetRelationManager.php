<?php

namespace App\Filament\Resources\CampaignResource\RelationManagers;

use App\Filament\Resources\TargetResource;
use App\Models\Target;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class TargetRelationManager extends RelationManager
{
    protected static string $relationship = 'targets';


    public static function getEloquentQuery(): Builder
    {
        return Target::AllTargets();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All target'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', true)),
            'paused' => Tab::make('Paused')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', false)),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Target Name')
                    ->required()
                    ->hiddenOn('edit')
                    ->maxLength(200),
                Forms\Components\TextInput::make('number')
                    ->label('Target Number')
                    ->required()
                    ->hiddenOn('edit')
                    ->minLength(10)
                    ->maxLength(13),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->hiddenOn('edit')
                    ->maxLength(200),
                Forms\Components\TextInput::make('daily_cap')
                    ->label('Daily Cap')
                    ->required()
                    ->hint('-1 = unlimited')
                    ->default(-1)
                    ->numeric()
                    ->minValue(-1)
                    ->maxValue(10000),
                Forms\Components\TextInput::make('monthlycap')
                    ->label('Monthly Cap')
                    ->required()
                    ->hint('-1 = unlimited')
                    ->default(-1)
                    ->numeric()
                    ->minValue(-1)
                    ->maxValue(10000),
                Forms\Components\TextInput::make('concurrent_calls')
                    ->label('Concurrent Calls')
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10000),

                Forms\Components\TextInput::make('priority')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\TextInput::make('ringtimeout')
                    ->label('Ring Timeout')
                    ->required()
                    ->numeric()
                    ->default('30')
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\TextInput::make('calltimeout')
                    ->label('Call Timeout')
                    ->required()
                    ->numeric()
                    ->default('30')
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Toggle::make('active')
                    ->label('Active')
                    ->default(true),
                Forms\Components\Hidden::make('campaignname')
                    ->default($this->ownerRecord->camp_name),
                Forms\Components\Hidden::make('calltype')
                    ->default('1'),
                Forms\Components\Hidden::make('callcontrol')
                    ->default('1'),
                Forms\Components\Hidden::make('creationdate')
                    ->default(function () { return now(); }),
                Forms\Components\Hidden::make('customerid')
                    ->default(function () { return auth()->id() ; }),
            ]);
    }

    public function table(Table $table): Table
    {
        return TargetResource::table($table)
            ->filters([
                TernaryFilter::make('active')
                ->label('Active')
                ->native(false)
                ->default(true)

            ])->persistFiltersInSession()

            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->color('secondary')
                 ->excludeAttributes(['name', 'number','description'])
                 ->form([
                     Forms\Components\TextInput::make('name')
                         ->label('Target Name')
                         ->required()
                         ->maxLength(200),
                     Forms\Components\TextInput::make('number')
                         ->label('Target Number')
                         ->required()
                         ->minLength(10)
                         ->maxLength(14),
                     Forms\Components\TextInput::make('description')
                         ->label('Description')
                         ->maxLength(200),
                 ])
                 ->beforeReplicaSaved(function (Target $replica, array $data): void {
                     $replica->fill($data);
                 }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
