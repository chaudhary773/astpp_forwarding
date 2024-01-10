<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallBlockResource\Pages;
use App\Filament\Resources\CallBlockResource\RelationManagers;
use App\Models\CallBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CallBlockResource extends Resource
{
    protected static ?string $model = CallBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customer_id', auth()->id());
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth()->id();
        return $data;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Number')
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(200),
                Forms\Components\Toggle::make('status')
                    ->label('Status')
                    ->default(1)
                    ->required(),
                Forms\Components\Hidden::make('customer_id')
                ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description'),
                Tables\Columns\ToggleColumn::make('status'),
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
            'index' => Pages\ManageCallBlocks::route('/'),
        ];
    }
}
