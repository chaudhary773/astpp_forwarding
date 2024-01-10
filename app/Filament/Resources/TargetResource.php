<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetResource\Pages;
use App\Filament\Resources\TargetResource\RelationManagers;
use App\Models\Campaign;
use App\Models\Did;
use App\Models\Target;
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
        return parent::getEloquentQuery()->where('customerid', auth()->id());
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
                Tables\Columns\TextColumn::make('daily_cap')
                    ->label('DC')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthlycap')
                    ->label("MC")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('concurrent_calls')
                    ->label('CC')
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
        return static::getModel()::count();
    }
}
