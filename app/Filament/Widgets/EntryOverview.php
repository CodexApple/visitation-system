<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Entry;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Widgets\TableWidget as BaseWidget;

class EntryOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Current Clock-In';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->poll('2s')
            ->query(
                Entry::query()
                    ->whereNull('date_out')
                    ->whereNull('time_out')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('users.name')->label('User'),
                Tables\Columns\TextColumn::make('date_in'),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration Since Time In')
                    ->getStateUsing(function ($record) {
                        $timeIn = Carbon::parse($record->date_in . ' ' . $record->time_in);
                        return $timeIn->diffForHumans(now(), [
                            'syntax' => Carbon::DIFF_ABSOLUTE,
                            'short' => true,
                        ]);
                    }),
            ]);
    }
}
