<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DidResource\Pages;
use App\Filament\Resources\DidResource\RelationManagers;
use App\Models\Did;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DidResource extends Resource
{
    protected static ?string $model = Did::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('accountid', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('accountid')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('parent_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('connectcost')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\TextInput::make('includedseconds')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('monthlycost')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->default(0.00000)
                    ->prefix('$'),
                Forms\Components\TextInput::make('init_inc')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('inc')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('extensions')
                    ->required()
                    ->maxLength(180),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\TextInput::make('provider_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('country_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('province')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('prorate')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('setup')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\TextInput::make('limittime')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('disconnectionfee')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\Textarea::make('variables')
                    ->required()
                    ->maxLength(16777215)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('options')
                    ->maxLength(40),
                Forms\Components\TextInput::make('maxchannels')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('chargeonallocation')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('allocation_bill_status')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('dial_as')
                    ->required()
                    ->maxLength(40),
                Forms\Components\Toggle::make('call_type')
                    ->required(),
                Forms\Components\TextInput::make('inuse')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('leg_timeout')
                    ->required()
                    ->numeric()
                    ->default(30),
                Forms\Components\DateTimePicker::make('assign_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('charge_upto')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_modified_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('connectcost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('includedseconds')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthlycost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('init_inc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extensions')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('provider_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prorate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('setup')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('limittime')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disconnectionfee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('options')
                    ->searchable(),
                Tables\Columns\TextColumn::make('maxchannels')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chargeonallocation')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('allocation_bill_status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dial_as')
                    ->searchable(),
                Tables\Columns\IconColumn::make('call_type')
                    ->boolean(),
                Tables\Columns\TextColumn::make('inuse')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('leg_timeout')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assign_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('charge_upto')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_modified_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDids::route('/'),
            'create' => Pages\CreateDid::route('/create'),
            'edit' => Pages\EditDid::route('/{record}/edit'),
        ];
    }
}
