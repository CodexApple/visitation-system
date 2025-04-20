<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Tables\Concerns\InteractsWithTable;
use TomatoPHP\FilamentLogger\Models\Activity;

class GenerateActivityReport extends Page implements \Filament\Tables\Contracts\HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'activity-report/{key}';

    protected static ?string $title = 'Activity Report';

    protected static bool $shouldRegisterNavigation  = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.generate-activity-report';

    public string $filterKey;
    public array $filters = [];

    public static function getRouteParameters()
    {
        return ['key'];
    }

    public function mount(string $key)
    {
        $this->filterKey = $key;
        $this->filters= session($key);

        if (!session()->has($key)) {
            abort(403, 'Unauthorized or session expired.');
        }
    }

    public static function getDate($date)
    {
        [$start, $end] = explode(' - ', $date);

        return [
            Carbon::createFromFormat('d/m/Y', trim($start))->toDateString(),
            Carbon::createFromFormat('d/m/Y', trim($end))->toDateString()
        ];
    }

    public function table (Table $table)
    {
        $filters = session($this->filterKey);
        $userId = $filters['user_id'] ?? null;

        [$start, $end] = explode(' - ', $filters['date_range']);

        $startDate = Carbon::createFromFormat('d/m/Y', trim($start))->toDateString();
        $endDate = Carbon::createFromFormat('d/m/Y', trim($end))->toDateString();

        return $table
            ->query(
                Activity::query()
                    ->where('model_id', $userId)
                    ->whereBetween('created_at', [$startDate, $endDate])
            )
            ->columns([
                Tables\Columns\TextColumn::make('method')
                    ->label(trans('filament-logger::messages.columns.method'))
                    ->description(fn($record) => '('.$record->status.') '.str($record->url)->remove(url('/'))),
                Tables\Columns\TextColumn::make('remote_address')
                    ->label(trans('filament-logger::messages.columns.remote_address'))
                    ->description(fn($record) => $record->model?->name)
                    ->icon('heroicon-o-globe-alt'),
                Tables\Columns\TextColumn::make('response_time')
                    ->label(trans('filament-logger::messages.columns.response_time'))
                    ->numeric(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans('filament-logger::messages.columns.created_at'))
                    ->description(fn($record) => $record->created_at->diffForHumans())
                    ->dateTime(),
            ])
            ->paginated(false);
    }
}
