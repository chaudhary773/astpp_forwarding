<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Z3d0X\FilamentLogger\Resources\ActivityResource as BaseActivityResource;

class ActivityResource extends BaseActivityResource
{



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make([
                        TextInput::make('causer_id')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                /** @phpstan-ignore-next-line */
                                return $component->state($record->causer?->first_name);
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.user')),

                        TextInput::make('subject_type')
                            ->afterStateHydrated(function ($component, ?Model $record, $state) {
                                /** @var Activity&ActivityModel $record */
                                return $state ? $component->state(Str::of($state)->afterLast('\\')->headline().' # '.$record->subject_id) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.subject')),

                        Textarea::make('description')
                            ->label(__('filament-logger::filament-logger.resource.label.description'))
                            ->rows(2)
                            ->columnSpan('full'),
                    ])
                        ->columns(2),
                ])
                    ->columnSpan(['sm' => 3]),

                Group::make([
                    Section::make([
                        Placeholder::make('log_name')
                            ->content(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->log_name ? ucwords($record->log_name) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.type')),

                        Placeholder::make('event')
                            ->content(function (?Model $record): string {
                                /** @phpstan-ignore-next-line */
                                return $record?->event ? ucwords($record?->event) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.event')),

                        Placeholder::make('created_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->content(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->created_at ? "{$record->created_at->format(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))}" : '-';
                            }),
                    ])
                ]),
                Section::make()
                    ->columns()
                    ->visible(fn ($record) => $record->properties?->count() > 0)
                    ->schema(function (?Model $record) {
                        /** @var Activity&ActivityModel $record */
                        $properties = $record->properties->except(['attributes', 'old']);

                        $schema = [];

                        if ($properties->count()) {
                            $schema[] = KeyValue::make('properties')
                                ->label(__('filament-logger::filament-logger.resource.label.properties'))
                                ->columnSpan('full');
                        }

                        if ($old = $record->properties->get('old')) {
                            $schema[] = KeyValue::make('old')
                                ->afterStateHydrated(fn (KeyValue $component) => $component->state($old))
                                ->label(__('filament-logger::filament-logger.resource.label.old'));
                        }

                        if ($attributes = $record->properties->get('attributes')) {
                            $schema[] = KeyValue::make('attributes')
                                ->afterStateHydrated(fn (KeyValue $component) => $component->state($attributes))
                                ->label(__('filament-logger::filament-logger.resource.label.new'));
                        }

                        return $schema;
                    }),
            ])
            ->columns(['sm' => 4, 'lg' => null]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->badge()
                    ->colors(static::getLogNameColors())
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->sortable(),

                TextColumn::make('event')
                    ->label(__('filament-logger::filament-logger.resource.label.event'))
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('filament-logger::filament-logger.resource.label.description'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->wrap(),

                TextColumn::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject'))
                    ->formatStateUsing(function ($state, Model $record) {
                        /** @var Activity&ActivityModel $record */
                        if (!$state) {
                            return '-';
                        }
                        return Str::of($state)->afterLast('\\')->headline().' # '.$record->subject_id;
                    }),

                TextColumn::make('causer.first_name')
                    ->label(__('filament-logger::filament-logger.resource.label.user')),

                TextColumn::make('causer.number')
                    ->label(__('Account Number')),

                TextColumn::make('created_at')
                    ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                    ->dateTime(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([])
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->options(static::getLogNameList()),

                SelectFilter::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject_type'))
                    ->options(static::getSubjectTypeList()),

                Filter::make('properties->old')
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['old']) {
                            return null;
                        }

                        return __('filament-logger::filament-logger.resource.label.old_attributes') . $data['old'];
                    })
                    ->form([
                        TextInput::make('old')
                            ->label(__('filament-logger::filament-logger.resource.label.old'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['old']) {
                            return $query;
                        }

                        return $query->where('properties->old', 'like', "%{$data['old']}%");
                    }),

                Filter::make('properties->attributes')
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['new']) {
                            return null;
                        }

                        return __('filament-logger::filament-logger.resource.label.new_attributes') . $data['new'];
                    })
                    ->form([
                        TextInput::make('new')
                            ->label(__('filament-logger::filament-logger.resource.label.new'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['new']) {
                            return $query;
                        }

                        return $query->where('properties->attributes', 'like', "%{$data['new']}%");
                    }),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('logged_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->displayFormat(config('filament-logger.date_format', 'd/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                    }),
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
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }
}
