<?php

namespace App\Filament\Resources\EntryResource\Pages;

use App\Filament\Pages\GenerateUserReport;
use App\Filament\Resources\EntryResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRecords;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Str;

class ManageEntries extends ManageRecords
{
    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_report')
                ->label('Generate Report')
                ->icon('heroicon-s-document-text')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->required()
                        ->label('Name')
                        ->options(User::orderBy('name', 'asc')->get(['id', 'name'])->pluck('name','id'))
                        ->preload()
                        ->searchable()
                        ->columnSpanFull(),
                    DateRangePicker::make('date_range')
                        ->label('Date Range')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $key = 'report_' . Str::uuid();
                    session()->put($key, $data);

                    return redirect()->route(GenerateUserReport::getRouteName(), ['key' => $key]);
                }),
            Actions\CreateAction::make(),
        ];
    }
}
