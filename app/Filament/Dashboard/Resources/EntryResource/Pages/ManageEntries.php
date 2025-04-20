<?php

namespace App\Filament\Dashboard\Resources\EntryResource\Pages;

use Filament\Forms;
use App\Models\Entry;
use Filament\Actions;
use App\Models\Purpose;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Dashboard\Resources\EntryResource;
use Filament\Support\Colors\Color;

class ManageEntries extends ManageRecords
{
    protected static string $resource = EntryResource::class;

    protected $listeners = ['update-timein-button' => '$refresh'];

    protected function getHeaderActions(): array
    {
        $entry = Entry::where('user_id', auth()->user()->id)
            ->whereDate('date_in', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        if ($entry && !$entry->time_out) {
            return [
                Actions\Action::make('clock_out')
                    ->label('Clock Out')
                    ->icon('heroicon-s-clock')
                    ->color(Color::Red)
                    ->action(function () use ($entry): void {
                        $entry->update([
                            'date_out' => now()->toDateString(),
                            'time_out' => now()->format('H:i:s'),
                        ]);

                        $this->dispatch('update-timein-button');
                    }),
            ];
        }

        return [
            Actions\Action::make('clock_in')
                ->label('Clock In')
                ->icon('heroicon-s-clock')
                ->color(Color::Teal)
                ->form([
                    Forms\Components\Grid::make(12)
                        ->schema([
                            Forms\Components\DatePicker::make('date_in')
                                ->default(now()->toDateString())
                                ->label('Date In')
                                ->columnSpan(6)
                                ->readOnly()
                                ->native(false)
                                ->required(),
                            Forms\Components\TimePicker::make('time_in')
                                ->default(now()->format('H:i:s'))
                                ->label('Time In')
                                ->columnSpan(6)
                                ->readOnly()
                                ->required(),
                        ]),

                    Forms\Components\Select::make('purpose_id')
                        ->required()
                        ->label('Purpose Template')
                        ->options(Purpose::orderBy('name', 'asc')->get(['id', 'name'])->pluck('name', 'id'))
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->label('Title')
                                ->autocomplete(false)
                                ->autocapitalize()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('description')
                                ->required()
                                ->label('Short Description')
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'orderedList',
                                    'bulletList',
                                    'redo',
                                    'undo'
                                ])
                                ->columnSpanFull(),
                        ])
                        ->createOptionUsing(fn(array $data): mixed => Purpose::create($data)->id)
                        ->afterStateUpdated(function (callable $set, $state) {
                            $template = Purpose::select('description')->find($state);
                            $set('description', $template->description ?? '');
                        })
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'orderedList',
                            'bulletList',
                            'redo',
                            'undo'
                        ])
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    Entry::create([
                        'user_id' => auth()->user()->id,
                        'date_in' => $data['date_in'],
                        'time_in' => $data['time_in'],
                        'purpose_id' => $data['purpose_id'],
                        'description' => $data['description'],
                    ]);
                }),
        ];
    }
}
