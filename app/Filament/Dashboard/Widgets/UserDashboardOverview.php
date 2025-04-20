<?php

namespace App\Filament\Dashboard\Widgets;

use App\Models\Entry;
use Illuminate\Support\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserDashboardOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '10s';

    public function getColumns(): int
    {
        return 2;
    }

    private function getCurrentMonth(): string
    {
        return Carbon::now()->format('F');
    }

    private function getTotalHours(): string
    {
        $totalSeconds = Entry::where('user_id', auth()->user()->id)->get()->reduce(function ($carry, $entry) {
            $timeIn = Carbon::parse($entry->time_in);
            $timeOut = Carbon::parse($entry->time_out);
            return $carry + $timeIn->diffInSeconds($timeOut);
        }, 0);

        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);
        $totalSeconds = $totalSeconds % 60;

        $formattedTime = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);

        return $formattedTime;
    }

    private function getTotalHoursForMonth(): string
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalTimeUserThisMonth = Entry::where('user_id', auth()->user()->id)
            ->whereBetween('date_in', [$startOfMonth, $endOfMonth])
            ->whereNotNull('time_out')
            ->get()
            ->reduce(function ($carry, $entry) {
                $timeIn = Carbon::parse($entry->time_in);
                $timeOut = Carbon::parse($entry->time_out);
                return $carry + $timeIn->diffInHours($timeOut);
        }, 0);

        return $totalTimeUserThisMonth;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Hours for ' . $this->getCurrentMonth(), $this->getTotalHoursForMonth())
                ->description('Total hours rendered per month')
                ->descriptionIcon('heroicon-o-clock', IconPosition::Before)
                ->chart([1, 3, 5, 10, 20, 40])
                ->color('success'),
            Stat::make('Hours Rendered', $this->getTotalHours())
                ->description('Total hours rendered as a whole')
                ->descriptionIcon('heroicon-o-clock', IconPosition::Before)
                ->chart([1, 3, 5, 10, 20, 40])
                ->color('success'),
        ];
    }
}
