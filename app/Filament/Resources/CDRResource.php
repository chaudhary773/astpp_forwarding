<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CDRResource\Pages;
use App\Filament\Resources\CDRResource\Widgets\CDROverview;
use App\Models\CDR;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CDRResource extends Resource
{
    protected static ?string $model = CDR::class;
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $label = 'Calls Report';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('customerid', auth()->id());
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callerid')
                    ->label('Caller#')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('did')
                    ->label('DID')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('forwardednumber')
                    ->label('Fwd. Number')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('call_start')
                    ->dateTime()
                    ->label('Call Start')
                    ->sortable(),
                Tables\Columns\TextColumn::make('call_answer')
                    ->dateTime()
                    ->label('Call Answer'),
                Tables\Columns\TextColumn::make('call_end')
                    ->label('Call End')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('campname')
                    ->label('Campaign')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('buyername')
                    ->label('Buyer')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('billseconds')
                    ->label('Bill Sec')
                 //   ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('ringseconds')
                    ->label('Ring Sec')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tta')
                    ->label('Tta')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('disposition')
//                    ->label('Disposition')
//                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('recordingfile')
                    ->label('Download')
                    ->color('info')
                    ->icon(static fn (CDR $record): string|null => $record->recordingfile !== null ? 'heroicon-m-cloud-arrow-down' : null)
                    ->url(fn (CDR $record) => env('RECORDING_URL') . "/{$record->recordingfile}"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('buyerid')
                    ->label('Buyer')
                    ->relationship('target', 'name',
                        fn (Builder $query) => $query->where('customerid', auth()->id())
                    )
                    ->preload()
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('campname')
                    ->label('Campaign')
                    ->relationship('campaign', 'camp_name',
                        fn (Builder $query) => $query->where('customer_id', auth()->id())
                    )
                    ->preload()
                    ->multiple()
                    ->searchable(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('call_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('call_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['call_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('call_start', '>=', $date),
                            )
                            ->when(
                                $data['call_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('call_end', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['call_from'] ?? null) {
                            $indicators['call_from'] = 'Call from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['call_until'] ?? null) {
                            $indicators['call_until'] = 'Call until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
//                Tables\Actions\Action::make('download')
//                    ->label('Download')
//                    // ->icon('heroicon-')
//                    ->action(function (Cdr $record, array $data): void {
//                            $record->download();
//                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['buyername', 'campname', 'did', 'callerid', 'forwardednumber'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCDRS::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

//    public static function getWidgets(): array
//    {
//        return [
//            CDROverview::class,
//        ];
//    }
}
