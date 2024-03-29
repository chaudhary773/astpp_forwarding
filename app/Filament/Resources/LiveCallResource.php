<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiveCallResource\Pages;
use App\Filament\Resources\LiveCallResource\RelationManagers;
use App\Models\LiveCall;
use DateTime;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LiveCallResource extends Resource
{
    protected static ?string $model = LiveCall::class;

    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customerid', auth()->id())->orderBy('start_time', 'desc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('answer_time')
                    ->label('Answer Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('callerid')
                    ->label('Caller#')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('did')
                    ->label('DID')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('target')
                    ->label('Target')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('campaign.camp_name')
                    ->label('Campaign')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('buyername')
                    ->label('Buyer')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('')
                    ->label('Duration')
                    ->getStateUsing(function (LiveCall $record) {
                        $startDate = new DateTime($record['answer_time']);
                        $endDate = new DateTime(now());
//                        $to = Carbon::createFromFormat('Y-m-d H:i:s', $record['answer_time']);
//                        $from = Carbon::createFromFormat('Y-m-d H:i:s', now());
                      // dd($to, $from);
                        $interval = $startDate->diff($endDate);
                        return $record->answered == 1 ? $interval->format('%H:%I:%S') : 0;
                    }),

                Tables\Columns\TextColumn::make('answered')
                    ->label('Call Status')
                    ->badge()
                    ->sortable()
                    ->getStateUsing(fn (LiveCall $record): string => $record->answered == 1 ? 'Answered' : 'Ringing')
                    ->colors([
                        'success' => 'Answered',
                        'warning' => 'Ringing',
                    ]),

            ])
            ->poll('10s')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('hangup')
                    ->label('')
                    ->icon(static fn () => 'heroicon-m-phone-x-mark')
                    ->color('danger')
                    ->action(fn (LiveCall $record, array $data) => $record->hangup())
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-m-phone-x-mark')
                    ->modalIconColor('danger')
                    ->modalHeading('Terminate Call')
                    ->modalDescription('Are you sure you\'d like to terminate the call? This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, terminate it')
                    ->successNotification(
                        Notification::make()
                            ->icon('heroicon-m-phone-x-mark')
                            ->danger()
                            ->title('Call Terminated')
                            ->body('The call has been successfully.'),
                    )
            ])
            ->bulkActions([

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
            'index' => Pages\ListLiveCalls::route('/'),
        ];
    }

   /* public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('customerid', auth()->id())->count();
    }*/

    protected function getTablePollingInterval(): ?string
    {
        return '10s';
    }
}
