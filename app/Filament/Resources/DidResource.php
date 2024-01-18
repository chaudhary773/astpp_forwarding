<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DidResource\Pages;
use App\Filament\Resources\DidResource\RelationManagers;
use App\Models\Campaign;
use App\Models\Did;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DidResource extends Resource
{
    protected static ?string $model = Did::class;
    protected static ?string $navigationGroup = 'Campaigns';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->with('campaigns')->where('accountid', auth()->id())->where('call_type', '=', 6);
    }



    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Strategy')
                    ->getStateUsing(fn (Did $record): string => $record->status == 0 ? 'Active' : 'Inactive')
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Inactive',
                        ]),
                Tables\Columns\TextColumn::make('campaign_id')
                    ->label('Campaign')
                    ->getStateUsing(function (Did $record) {
                         return $record->campaigns->isNotEmpty() ? $record->campaigns->last()->camp_name : '';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('assign_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_modified_date')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
               // Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('status')
                    ->label('Toggle')
                   // ->icon('heroicon-')
                    ->action(function (Did $record, array $data): void {
                        $record->status = $record->status ? 0 : 1;
                        $record->save();
                    }),

                Tables\Actions\Action::make('attach')
                    ->label('Assign')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        Select::make('campaign_id')
                            ->label('Campaigns')
                            ->native(false)
                            ->searchable()
                            ->options(Campaign::where('customer_id', auth()->id())->pluck('camp_name', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data, Did $record): void {
                        $record->campaigns()->attach($data['campaign_id']);
                        $record->save();
                    }),
                Tables\Actions\Action::make('detach')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(true)
                    ->action(function (array $data, Did $record): void {
                        $record->campaigns()->detach();
                        $record->save();
                    })

            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::allDids()->count();
    }
}
