<style>
    @media print {
        body * {
            visibility: hidden;
        }

        /* Ensure header and footer are visible */
        .print-header,
        .print-header *,
        .print-footer,
        .print-footer *,
        .fi-ta-table,
        .fi-ta-table * {
            visibility: visible;
        }

        /* Layout adjustments */
        .print-header {
            position: absolute;
            top: 0;
            width: 100%;
            text-align: center;
        }

        .fi-ta-table {
            margin-top: 60px; /* Adjust based on the header height */
            width: 100%;
            margin-bottom: 60px; /* Space for the footer */
        }

        .print-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        /* Optional: Add a page break after the table if it's too large */
        .fi-ta-table {
            page-break-after: always;
        }
    }
</style>

<x-filament::page>
    <div class="print-header text-center mb-6">
        <h1 class="text-3xl font-extrabold text-gray-800">Transaction Logs</h1>

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

    {{ $this->table }}

    <div class="print-footer mt-16 text-sm text-gray-600">
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
</x-filament::page>
