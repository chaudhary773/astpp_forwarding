<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetResource\Pages;
use App\Filament\Resources\TargetResource\RelationManagers;
use App\Models\Campaign;
use App\Models\CDR;
use App\Models\DailyCdr;
use App\Models\Did;
use App\Models\LiveCall;
use App\Models\Target;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static ?string $navigationGroup = 'Campaigns';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return Target::AllTargets();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->description(fn(Target $record): string => $record->name ?? '')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('')
                    ->label('TA')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn(Target $record) => self::getTodayAnswered($record))
                    ->sortable(),

                Tables\Columns\TextColumn::make('today_missed')
                    ->label('TM')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn(Target $record) => self::getTodayMissed($record))
                    ->sortable(),

                Tables\Columns\TextColumn::make('daily_cap')
                    ->label('DC')
                    ->formatStateUsing(fn(Target $record) => self::getDailyCap($record))
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthlycap')
                    ->label("MC")
                    ->html(true)
                    ->formatStateUsing(fn(Target $record) => self::getMonthlyCap($record))
                    ->sortable(),
                Tables\Columns\TextColumn::make('concurrent_calls')
                    ->label('CC')
                    ->formatStateUsing(fn(Target $record) => self::getConcurrentCap($record))
                    ->sortable(),

                Tables\Columns\TextColumn::make('campaign.camp_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('priority'),
                Tables\Columns\TextColumn::make('ringtimeout')
                    ->label('Ring Timeout')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('calltimeout')
                    ->label('Call Timeout')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('active')
                    ->label('Status')
                ->sortable(),
                Tables\Columns\TextColumn::make('creationdate')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('modifieddate')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->color('secondary')
                    ->excludeAttributes(['campaign_id',])
                    ->form([
                        Forms\Components\Select::make('campaign_id')
                            ->label('Select Campaign')
                            ->native(false)
                            ->relationship('campaign', 'campaign_id')
                            ->options(fn(): array => Campaign::where('customer_id', auth()->id())->pluck('camp_name', 'id')->toArray())
                            ->required(),
                    ])
                    ->beforeReplicaSaved(function (Target $replica, array $data): void {
                        $data['campaignname'] = Campaign::find($data['campaign_id'])->camp_name;
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

    private static function getTodayAnswered(Target $record): ?string
    {

            return DailyCdr::where('campid', $record->campaign?->id)
                ->where('buyerid', $record->id)
                ->where('buyernumber', $record->number)
                ->where('customerid', auth()->id())
                ->whereDate('date', now()->toDateString())
                ->where('missed', 0)
                ->count('buyernumber');
    }

    private static function getTodayMissed(Target $record): ?string
    {

            return DailyCdr::where('campid', $record->campaign?->id)
                ->where('buyerid', $record->id)
                ->where('buyernumber', $record->number)
                ->where('customerid', auth()->id())
                ->whereDate('date', now()->toDateString())
                ->where('missed', 1)
                ->count('buyernumber');
    }

    private static function getDailyCap(Target $record): ?string
    {

            $dailyCount = DailyCdr::where('campid', $record->campaign?->id)
                ->where('buyerid', $record->id)
                ->where('buyernumber', $record->number)
                ->where('customerid', auth()->id())
                ->whereDate('date', '=', now()->toDateString())
                ->count('buyernumber');

            return $record->daily_cap == -1 ? $dailyCount . '/UL' : $dailyCount . '/' . $record->daily_cap;

    }

    private static function getMonthlyCap(Target $record): ?string
    {

            $month = now()->format('Y-m');
            $monthlyCount = CDR::where('campid', $record->campaign?->id)
                ->where('buyerid', $record->id)
                ->where('customerid', auth()->id())
                ->where('forwardednumber', $record->number)
                ->where('month', $month)
                ->count('forwardednumber');
            return $record->monthlycap == -1 ? $monthlyCount . '/UL' : $monthlyCount . '/' . $record->monthlycap;
    }

    private static function getConcurrentCap(Target $record): ?string
    {
            $CCcount = LiveCall::where('campid', $record->campaign?->id)
                ->where('buyerid', $record->id)
                ->where('target', $record->number)
                ->where('customerid', auth()->id())
                ->count('target');
            return $record->concurrent_calls == 0 ? $CCcount . '/UL' : $CCcount . '/' . $record->concurrent_calls;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Target Name')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('number')
                                    ->label('Target Number')
                                    ->required()
                                    ->numeric()
                                    ->minLength(10)
                                    ->maxLength(13),
                                Forms\Components\Select::make('campaign_id')
                                    ->label('Campaign')
                                    ->relationship('campaign', 'campaign_id')
                                    ->options(fn(): array => Campaign::where('customer_id', auth()->id())->pluck('camp_name', 'id')->toArray())
                                    ->required(),

                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('daily_cap')
                                    ->label('Daily Cap')
                                    ->required()
                                    ->hint('-1 = unlimited')
                                    ->default(-1)
                                    ->numeric()
                                    ->minValue(-1)
                                    ->maxValue(10000)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if ($get('daily_cap') >= 1 ) {
                                            return;
                                        }
                                        $set('daily_cap', -1);
                                    }),
                                Forms\Components\TextInput::make('monthlycap')
                                    ->label('Monthly Cap')
                                    ->required()
                                    ->hint('-1 = unlimited')
                                    ->default(-1)
                                    ->numeric()
                                    ->minValue(-1)
                                    ->maxValue(10000)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if ($get('monthlycap') >= 1 ) {
                                            return;
                                        }
                                        $set('monthlycap', -1);
                                    }),
                                Forms\Components\TextInput::make('concurrent_calls')
                                    ->label('Concurrent Calls')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(10000),

                                Forms\Components\TextInput::make('priority')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(100),

                                Forms\Components\TextInput::make('ringtimeout')
                                    ->label('Ring Timeout')
                                    ->required()
                                    ->numeric()
                                    ->default(30)
                                    ->minValue(1)
                                    ->minValue(0)
                                    ->maxValue(100),

                                Forms\Components\TextInput::make('calltimeout')
                                    ->label('Call Timeout')
                                    ->required()
                                    ->numeric()
                                    ->default(30)
                                    ->minValue(1)
                                    ->maxValue(100),
                                Forms\Components\Toggle::make('active')
                                    ->label('Active')
                                    ->default(true),
                                Forms\Components\Hidden::make('creationdate')
                                    ->default(function () {
                                        return now();
                                    }),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTargets::route('/'),
            'create' => Pages\CreateTarget::route('/create'),
            'edit' => Pages\EditTarget::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::allTargets()->count();
    }

}
