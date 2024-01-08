<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampDidResource\Pages;
use App\Filament\Resources\CampDidResource\RelationManagers;
use App\Models\CampDid;
use App\Models\Did;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampDidResource extends Resource
{
    protected static ?string $model = CampDid::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

//                Forms\Components\Select::make('did_id')
//                    ->label('DID')
//                    ->native(false)
//                    ->options(fn() => Did::where('accountid', auth()->id())->pluck('number', 'id'))
//                    ->multiple()
//                    ->required(),

                Forms\Components\Select::make('did_id')
                    ->relationship('did', 'number')
                    ->options(fn() => Did::where('accountid', auth()->id())->pluck('number', 'id'))
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Tables\Grouping\Group::make('campaign.camp_name')
                    ->collapsible(true)
                    ->titlePrefixedWithLabel(false)
            )
            ->columns([
                Tables\Columns\TextColumn::make('campaign.camp_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('did.number')
                    ->searchable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCampDids::route('/'),
        ];
    }
}
