<?php

namespace App\Filament\Exports;

use App\Models\CDR;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CDRExporter extends Exporter
{
    protected static ?string $model = CDR::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('callerid')->label('Caller#'),
            ExportColumn::make('did')->label('DID'),
            ExportColumn::make('forwardednumber')->label('Fwd. Number'),
            ExportColumn::make('billseconds')->label('Bill Sec.'),
            ExportColumn::make('ringseconds')->label('Ring Sec.'),
            ExportColumn::make('disposition')->label('Disposition'),
            ExportColumn::make('call_start')->label('Call Start'),
            ExportColumn::make('call_answer')->label('Call Answer'),
            ExportColumn::make('call_end')->label('Call End'),
            ExportColumn::make('missed') ->formatStateUsing(fn (string $state): string => $state == 1 ? 'Missed' : 'Answered'),
            ExportColumn::make('buyername')->label('Buyer'),
            ExportColumn::make('campname')->label('Campaign'),
            ExportColumn::make('tta')->label('Tta'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your CDR export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) .
            ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
