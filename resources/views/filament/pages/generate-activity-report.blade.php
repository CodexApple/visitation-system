<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .print-header,
        .print-header *,
        .print-footer,
        .print-footer *,
        .fi-ta-table,
        .fi-ta-table * {
            visibility: visible;
        }

        .print-header {
            display: block;
        }

        .print-header:not(:first-child) {
            display: none;
        }

        .print-footer {
            page-break-before: avoid;
            margin-top: 20px;
        }

        .fi-ta-table {
            width: 100%;
            page-break-inside: avoid;
        }
    }
</style>

<x-filament-panels::page>
    <div class="print-header text-center">
        <h1 class="text-3xl font-extrabold text-gray-800">Activity Logs</h1>

        <h2 class="text-lg font-semibold text-gray-600 mt-1">
            {{ \App\Models\User::find($this->filters['user_id'])->name ?? 'N/A' }}
        </h2>

        <div class="mt-4 text-sm text-gray-500 space-y-1">
            <p><span class="font-medium">Printed On:</span> {{ now()->toDayDateTimeString() }}</p>
            <p>
                <span class="font-medium">Date Range:</span>
                {{ $this->getDate($this->filters['date_range'])[0] }} to
                {{ $this->getDate($this->filters['date_range'])[1] }}
            </p>
        </div>
    </div>

    <div class="print-body">
        {{ $this->table }}
    </div>

    <div class="print-footer text-center text-sm text-gray-600">
        <div class="border-t border-gray-300 pt-6 text-center">
            <p class="mb-12 italic">This report was system-generated and does not require a signature.</p>

            <div class="flex justify-between items-center px-16 mt-16">
                <div class="text-center">
                    <div class="border-t-2 border-gray-700 w-48 mx-auto"></div>
                    <p class="mt-2">Prepared by</p>
                    <p class="mt-2">{{ auth()->user()->name }}</p>
                </div>

                <div class="text-center">
                    <div class="border-t-2 border-gray-700 w-48 mx-auto"></div>
                    <p class="mt-2">Approved by</p>
                    <p class="mt-2"></p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
