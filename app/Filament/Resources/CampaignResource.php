<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Models\Campaign;
use App\Models\Did;
use App\Models\Target;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationGroup = 'Campaigns';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customer_id', auth()->id());
    }

    public static function form(Form $form): Form
    {

        $unassignedDids = Did::allDids()->whereDoesntHave('campaigns')->pluck('number', 'id')->toArray();
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('camp_name')
                                    ->label('Campaign Name')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('description')
                                    ->required()
                                    ->maxLength(200),

                                Forms\Components\Select::make('did_id')
                                    ->relationship('dids', 'number')
                                    ->multiple()
                                    ->options(fn() => $unassignedDids)
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('camp_mode')
                                    ->label('Campaign Strategy')
                                    ->required()
                                    ->native(false)
                                    ->options([1 => 'Priority', 2 => 'Weight']),
                                Forms\Components\TextInput::make('call_timeout')
                                    ->label('Call Timeout')
                                    ->required()
                                    ->default(60)
                                    ->numeric(),
                                Forms\Components\TextInput::make('ring_timeout')
                                    ->label('Ring Timeout')
                                    ->required()
                                    ->default(30)
                                    ->numeric(),
                                Forms\Components\Hidden::make('threading')
                                    ->default(1)
                                    ->label('Call Threading'),
                                Forms\Components\Toggle::make('active')
                                    ->default(true),
                                Forms\Components\Hidden::make('customer_id')
                                    ->default( auth()->id() ),
                            ]),
                    ])
                    ->collapsed(fn (string $operation) => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('camp_name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                Tables\Columns\TextColumn::make('camp_mode')
                    ->label('Strategy')
                    ->badge()
                    ->getStateUsing(fn(Campaign $record): string => $record->camp_mode == 1 ? 'Priority' : 'Weight')
                    ->colors([
                        'success' => 'Priority',
                    ]),
                //    Tables\Columns\BooleanColumn::make('threading'),
                Tables\Columns\TextColumn::make('call_timeout'),
                Tables\Columns\TextColumn::make('ring_timeout'),
                Tables\Columns\TextColumn::make('live calls')
                    ->label('Live calls')
                    ->getStateUsing(fn (Campaign $record) => $record->liveCalls->count('campid')),
                Tables\Columns\TextColumn::make('targets.concurrent_calls')
                    ->getStateUsing(fn (Campaign $record) => $record->targets->where('active', 1)->sum('concurrent_calls'))
                    ->label('CC'),
                Tables\Columns\ToggleColumn::make('active'),
                //   Tables\Columns\TextColumn::make('targets_exists')->exists('targets', 'id'),
                Tables\Columns\TextColumn::make('create_date')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('modified_date')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\Action::make('add_target')
//                    ->label('Add Target')
//                    ->icon('heroicon-o-users')
//                    ->action(function (Campaign $record, array $data): void {
//
//                       $target = Target::firstOrCreate([
//                            'name' => $data['name'],
//                           'number' => $data['number'],
//                           'campaign_id' => $record->id,
//                           'customerid' => auth()->id(),
//                           'active' => $data['active'],
//                           'daily_cap' => $data['daily_cap'],
//                           'monthlycap' => $data['monthlycap'],
//                           'concurrent_calls' => $data['concurrent_calls'],
//                           'priority' => $data['priority'],
//                           'calltimeout' => '60',
//                           'ringtimeout' => '30',
//                           'callcontrol' => '1',
//                           'calltype' => '1',
//                           'campaignname' => $record->camp_name,
//                        ]);
//                    })
//                    ->form([
//                        Forms\Components\TextInput::make('name')
//                            ->required()
//                            ->maxLength(200),
//                        Forms\Components\TextInput::make('number')
//                            ->required()
//                            ->maxLength(14),
//                        Forms\Components\TextInput::make('daily_cap')
//                            ->label('Daily Cap')
//                            ->required()
//                            ->hint('0 = unlimited')
//                            ->default(1)
//                            ->numeric()
//                            ->minValue(0)
//                            ->maxValue(100),
//                        Forms\Components\TextInput::make('monthlycap')
//                            ->label('Monthly Cap')
//                            ->required()
//                            ->hint('0 = unlimited')
//                            ->default(1)
//                            ->numeric()
//                            ->minValue(0)
//                            ->maxValue(100),
//                        Forms\Components\TextInput::make('concurrent_calls')
//                            ->label('Concurrent Calls')
//                            ->required()
//                            ->default(1)
//                            ->numeric()
//                            ->minValue(0)
//                            ->maxValue(100),
//
//                        Forms\Components\TextInput::make('priority')
//                            ->required()
//                            ->numeric()
//                            ->default(1)
//                            ->minValue(0)
//                            ->maxValue(100),
//
//                        Forms\Components\TextInput::make('ringtimeout')
//                            ->label('Ring Timeout')
//                            ->required()
//                            ->numeric()
//                            ->default('30')
//                            ->minValue(0)
//                            ->maxValue(100),
//
//                        Forms\Components\TextInput::make('calltimeout')
//                            ->label('Call Timeout')
//                            ->required()
//                            ->numeric()
//                            ->default('30')
//                            ->minValue(0)
//                            ->maxValue(100),
//
//                        Forms\Components\Toggle::make('active')
//                            ->label('Active')
//                            ->default(true),
//                    ]),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TargetRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('customer_id', auth()->id())->count();
    }

    public function getTargetCounts($record): Builder
    {
        return Target::where('campaign_id', $record->id)->get();
    }
}
