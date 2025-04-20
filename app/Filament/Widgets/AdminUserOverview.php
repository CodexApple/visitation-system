<?php

namespace App\Filament\Widgets;

use App\Models\Entry;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AdminUserOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '10s';

    // protected int | string | array $columnSpan = 'full';

    public function getColumns(): int
    {
        return 2;
    }

    public function getTotalClockedInUsers(): int
    {
        $count = Entry::query()
                ->whereNull('date_out')
                ->whereNull('time_out')
                ->latest()
                ->count();

        return $count;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Registered Users', User::select("id")->get()->count())
                ->description('Users registered on our system')
                ->descriptionIcon('heroicon-o-users', IconPosition::Before)
                ->chart([1, 3, 5, 10, 20, 40])
                ->color('success'),

            Stat::make('Clocked In Users',  $this->getTotalClockedInUsers())
                ->description('Total amount of clocked in users')
                ->descriptionIcon('heroicon-o-clock', IconPosition::Before)
                ->chart([1, 3, 5, 10, 20, 40])
                ->color('success'),
        ];
    }
}
