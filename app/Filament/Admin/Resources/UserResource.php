<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 0);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('reseller_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pricelist_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\Toggle::make('sweep_id')
                    ->required(),
                Forms\Components\DateTimePicker::make('creation')
                    ->required(),
                Forms\Components\TextInput::make('credit_limit')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\Toggle::make('posttoexternal')
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(40),
                Forms\Components\TextInput::make('company_name')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('address_1')
                    ->maxLength(80),
                Forms\Components\TextInput::make('address_2')
                    ->required()
                    ->maxLength(80),
                Forms\Components\TextInput::make('postal_code')
                    ->maxLength(12),
                Forms\Components\TextInput::make('province')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('city')
                    ->maxLength(20),
                Forms\Components\TextInput::make('country_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('telephone_1')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('telephone_2')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(80),
                Forms\Components\TextInput::make('language_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('currency_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('maxchannels')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('cps')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('dialed_modify')
                    ->required()
                    ->maxLength(16777215)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('type'),
                Forms\Components\TextInput::make('timezone_id')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('inuse')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('deleted')
                    ->required(),
                Forms\Components\TextInput::make('notify_credit_limit')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('notify_flag')
                    ->required(),
                Forms\Components\TextInput::make('notify_email')
                    ->email()
                    ->required()
                    ->maxLength(80),
                Forms\Components\TextInput::make('commission_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('invoice_day')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('pin')
                    ->required()
                    ->maxLength(20),
                Forms\Components\DateTimePicker::make('first_used')
                    ->required(),
                Forms\Components\DateTimePicker::make('expiry')
                    ->required(),
                Forms\Components\TextInput::make('validfordays')
                    ->required()
                    ->numeric()
                    ->default(3652),
                Forms\Components\TextInput::make('local_call_cost')
                    ->required()
                    ->numeric()
                    ->default(0.00000),
                Forms\Components\Toggle::make('pass_link_status')
                    ->required(),
                Forms\Components\Toggle::make('local_call')
                    ->required(),
                Forms\Components\TextInput::make('charge_per_min')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Toggle::make('is_recording')
                    ->required(),
                Forms\Components\Toggle::make('allow_ip_management')
                    ->required(),
                Forms\Components\TextInput::make('std_cid_translation')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('did_cid_translation')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('tax_number')
                    ->maxLength(100),
                Forms\Components\TextInput::make('secret')
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),


                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->getStateUsing(fn (User $record): bool => $record->status == 0 ? true : false),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('USD'),
                Tables\Columns\IconColumn::make('is_recording')
                    ->boolean(),
                Tables\Columns\TextColumn::make('secret')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creation')
                    ->label('Created At')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
