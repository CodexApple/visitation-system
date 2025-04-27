<?php

namespace App\Filament\Resources\EntryResource\Pages;

use Str;
use Filament\Forms;
use App\Models\User;
use App\Models\Entry;
use Filament\Actions;
use Illuminate\Support\Carbon;
use Spatie\Browsershot\Browsershot;
use App\Filament\Resources\EntryResource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\GenerateUserReport;
use Filament\Resources\Pages\ManageRecords;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ManageEntries extends ManageRecords
{
    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_report')
                ->label('Download Report')
                ->icon('heroicon-s-document-text')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->required()
                        ->label('Name')
                        ->options(
                            collect(['0' => 'All Users'])->merge(
                                User::orderBy('name', 'asc')->get(['id', 'name'])->pluck('name', 'id')
                            )
                        )
                        ->preload()
                        ->searchable()
                        ->columnSpanFull(),
                    DateRangePicker::make('date_range')
                        ->label('Date Range')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Download')
                ->modalCancelActionLabel('Close')
                ->action(function (array $data) {
                    $key = Str::uuid()->toString();

                    [$start, $end] = explode(' - ', $data['date_range']);
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($start))->toDateString();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($end))->toDateString();

                    $userId = $data['user_id'];

                    $user_data = User::with('identifiers')
                        ->where('id', $data['user_id'])
                        ->get();

                    $query_data = Entry::with(['purposes', 'users']) // eager load relationships
                        ->when($userId != 0, function (Builder $q) use ($userId) {
                            $q->where('user_id', $userId);
                        })
                        ->whereBetween('date_in', [$startDate, $endDate])
                        ->get();

                    // $query_data = Entry::with('purposes')
                    //     ->where('user_id', $data['user_id'])
                    //     ->whereBetween('date_in', [$startDate, $endDate])
                    //     ->get();

                    $data['query'] = $query_data;
                    $data['user'] = $user_data;

                    session()->put($key, $data);
                    $pdfPath = storage_path('app\\public\\reports\\' . Str::uuid() . '.pdf');
                    $template = view('report', ['key' => $key])->render();

                    Browsershot::html($template)
                        ->format('A4')
                        ->save($pdfPath);

                    return response()->download($pdfPath)->deleteFileAfterSend();
                    // return redirect()->route('report', ['key' => $key]);
                }),
            Actions\CreateAction::make(),
        ];
    }
}
