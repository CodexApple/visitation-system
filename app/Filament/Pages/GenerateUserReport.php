<?php

namespace App\Filament\Pages;

use App\Models\Entry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class GenerateUserReport extends Page implements \Filament\Tables\Contracts\HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'report-generation/{key}';

    protected static ?string $title = 'Report Generation';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation  = false;

    protected static string $view = 'filament.pages.generate-user-report';

    public string $filterKey;
    public array $filters = [];

    public static function getRouteParameters(): array
    {
        return ['key'];
    }

    public function mount(string $key)
    {
        $this->filterKey = $key;
        $this->filters = session($key);

        if (!session()->has($key)) {
            abort(403, 'Unauthorized or session expired.');
        }
    }

    public static function getDate($date): array
    {
        [$start, $end] = explode(' - ', $date);

        return [
            Carbon::createFromFormat('d/m/Y', trim($start))->toDateString(),
            Carbon::createFromFormat('d/m/Y', trim($end))->toDateString()
        ];
    }

    public function table(Table $table): Table
    {
        $filters = session($this->filterKey);
        $userId = $filters['user_id'] ?? null;

        [$start, $end] = explode(' - ', $filters['date_range']);

        $startDate = Carbon::createFromFormat('d/m/Y', trim($start))->toDateString();
        $endDate = Carbon::createFromFormat('d/m/Y', trim($end))->toDateString();

        return $table
            ->query(
                Entry::query()
                    ->where('user_id', $userId)
                    ->whereBetween('date_in', [$startDate, $endDate])
            )
            ->columns([
                Tables\Columns\TextColumn::make('date_in')->date(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('date_out')->date(),
                Tables\Columns\TextColumn::make('time_out'),
                // Tables\Columns\TextColumn::make('duration')
                //     ->label('Duration Since Time In')
                //     ->getStateUsing(function ($record) {
                //         $timeIn = Carbon::parse($record->date_in . ' ' . $record->time_in);
                //         return $timeIn->diffForHumans(now(), [
                //             'syntax' => Carbon::DIFF_ABSOLUTE,
                //             'short' => true,
                //         ]);
                //     }),
                Tables\Columns\TextColumn::make('purposes.name')
                    ->label('Purpose'),
                Tables\Columns\TextColumn::make('users.identifiers.ip_address')
                    ->label('IP Address'),
            ])
            ->paginated(false);
    }
}
