<?php
namespace App\Filament\Resources;
use App\Filament\Exports\CDRExporter;
use App\Filament\Resources\CDRResource\Pages;
use App\Models\CDR;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction as DefaultExportBulkAction;
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
        return parent::getEloquentQuery()->where('customerid', auth()->id())->orderBy('call_start', 'desc');
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('ringseconds')
                    ->label('Ring Sec'),
                Tables\Columns\TextColumn::make('missed')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (CDR $record): string =>  $record->missed == 1 ? 'Missed' : 'Answered' )
                    ->colors([
                        'success' => 'Answered',
                        'danger' => 'Missed',
                    ])
                    ->icons([
                        'heroicon-s-phone' => 'Answered',
                        'heroicon-s-phone-x-mark' => 'Missed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('tta')
                    ->label('Tta')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('recordingfile')
                    ->label('Download')
                    ->color('info')
                    ->icon( fn (CDR $record): string|null => $record->call_answer === null ? null : 'heroicon-m-cloud-arrow-down')
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
                Tables\Filters\Filter::make('call_start')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('call_start', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['call_from'] ?? null) {
                            $indicators['call_from'] = 'Call from ' . Carbon::parse($data['call_from'])->toFormattedDateString();
                        }
                        if ($data['call_until'] ?? null) {
                            $indicators['call_until'] = 'Call until ' . Carbon::parse($data['call_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DefaultExportBulkAction::make()
                        ->exporter(CDRExporter::class)
                ]),
            ]);
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

}
