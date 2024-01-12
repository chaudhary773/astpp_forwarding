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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return Target::AllTargets();
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Target Name')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('number')
                    ->label('Target Number')
                    ->required()
                    ->maxLength(14),
//                Forms\Components\TextInput::make('buyer_group_id')
//                    ->maxLength(200),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->maxLength(200),
                Forms\Components\TextInput::make('daily_cap')
                    ->label('Daily Cap')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('monthlycap')
                    ->label('Monthly Cap')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('concurrent_calls')
                    ->label('Concurrent Calls')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\TextInput::make('priority')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\TextInput::make('ringtimeout')
                    ->label('Ring Timeout')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\TextInput::make('calltimeout')
                    ->label('Call Timeout')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Toggle::make('active')
                ->label('Active')
                ->default(true),
                Forms\Components\Hidden::make('creationdate')
                ->default(function () { return now(); }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Tables\Grouping\Group::make('campaign.camp_name')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('active'),

                Tables\Columns\BadgeColumn::make('')
                    ->label('Today Answered')
                    ->color('success')
                    ->getStateUsing(fn(Target $record) => self::getTodayAnswered($record))
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('ad')
                    ->label('Today Missed')
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('priority'),
                Tables\Columns\TextColumn::make('ringtimeout'),
                Tables\Columns\TextColumn::make('calltimeout'),
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

    private static function getMonthlyCap(Target $record): string
    {
        $month = now()->format('Y-m');
        $monthlyCount = CDR::where('campid', $record->campaign->id)
            ->where('buyerid', 1)
            ->where('customerid', auth()->id())
            ->where('month', $month)
            ->count('forwardednumber');
        return  $record->monthlycap == 0 ? $monthlyCount .'/UL' :  $monthlyCount .'/'. $record->monthlycap;
    }

    private static function getDailyCap(Target $record): string
    {
        $dailyCount = DailyCdr::where('campid',  $record->campaign->id)
            ->where('buyerid', 1)
            ->where('buyernumber', $record->number)
            ->where('customerid', auth()->id())
            ->whereDate('date', '=', now()->toDateString())
            ->count('buyernumber');

        return  $record->daily_cap == 0 ? $dailyCount .'/UL' :  $dailyCount .'/'. $record->daily_cap;
    }

    private static function getTodayAnswered(Target $record): string
    {
       return DailyCdr::where('campid', $record->campaign->id)
            ->where('buyerid', 1)
            ->where('buyernumber', $record->number)
            ->where('customerid', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->where('missed', 0)
            ->count('buyernumber');
    }

    private static function getTodayMissed(Target $record): string
    {
        return DailyCdr::where('campid' , $record->campaign->id)
            ->where('buyerid', 1)
            ->where('buyernumber', $record->number)
            ->where('customerid', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->where('missed', 1)
            ->count('buyernumber');
    }

    private static function getConcurrentCap(Target $record): string
    {
        $CCcount = LiveCall::where('campid' , $record->campaign->id)
            ->where('buyerid', 1)
            ->where('target', $record->number)
            ->where('customerid', auth()->id())
            ->count('target');
        return  $record->concurrent_calls == 0 ? $CCcount .'/UL' :  $CCcount .'/'. $record->concurrent_calls;
    }

}
