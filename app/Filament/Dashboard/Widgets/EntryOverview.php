<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Entry;
use App\Models\Purpose;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Widgets\TableWidget as BaseWidget;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class EntryOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected $listeners = ['update-timein-button' => '$refresh'];

    public function getTableHeaderActions(): array
    {
        $entry = Entry::where('user_id', auth()->user()->id)
            ->whereDate('date_in', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        if ($entry && !$entry->time_out) {
            return [
                Tables\Actions\Action::make('clock_out')
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
            Tables\Actions\Action::make('clock_in')
                ->label('Clock In')
                ->icon('heroicon-s-clock')
                ->color(Color::Teal)
                ->form(fn () => $this->getFormSchema())
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

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(12)
                ->schema([
                    Forms\Components\DatePicker::make('date_in')
                        ->default(now()->toDateString())
                        ->label('Date In')
                        ->columnSpan(6)
                        ->readOnly()
                        ->native(false)
                        ->hiddenOn(['edit'])
                        ->required(),
                    Forms\Components\TimePicker::make('time_in')
                        ->default(now()->format('H:i:s'))
                        ->label('Time In')
                        ->columnSpan(6)
                        ->readOnly()
                        ->native(false)
                        ->hiddenOn(['edit'])
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
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Entry::query()->where('user_id', auth()->user()->id))
            ->columns([
                Tables\Columns\TextColumn::make('date_in')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('date_out')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('purposes.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                DateRangeFilter::make('created_at'),
            ])
            ->headerActions($this->getTableHeaderActions())
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form(fn () => $this->getFormSchema()),
                Tables\Actions\EditAction::make()
                    ->form(fn () => $this->getFormSchema()),
            ]);
    }
}
