<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntryResource\Pages;
use App\Filament\Resources\EntryResource\RelationManagers;
use App\Models\Entry;
use App\Models\Purpose;
use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntryResource extends Resource
{
    protected static ?string $model = Entry::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?string $navigationLabel = 'Entry Log';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->label('Name')
                    ->options(User::orderBy('name', 'asc')->get(['id', 'name'])->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('date_in')
                    ->label('Date In')
                    ->default(now()->toDateString())
                    ->required(),
                Forms\Components\TimePicker::make('time_in')
                    ->label('Clock In')
                    ->default(now()->format('H:i:s'))
                    ->required(),
                Forms\Components\DatePicker::make('date_out')
                    ->label('Date Out')
                    ->hiddenOn(['create']),
                Forms\Components\TimePicker::make('time_out')
                    ->label('Clock Out')
                    ->hiddenOn(['create']),
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
                    ->createOptionUsing(fn (array $data): mixed => Purpose::create($data)->id)
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('purposes.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_in')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('date_out')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('edit_state')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEntries::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
