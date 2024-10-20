<?php

namespace App\Listeners;

use App\Events\UpdateStockData;
use App\Http\Services\StockManagerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncStockDataListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Maximum number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Maximum number of seconds job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create the event listener.
     */
    public function __construct(protected StockManagerService $stockManagerService) {}

    /**
     * Handle the event.
     */
    public function handle(UpdateStockData $event): void
    {
        Log::info(sprintf('Updating stock data for symbol "%s ..."', $event->symbol));

        // If the function throws an exception, the job will automatically be released back onto the queue
        $this->stockManagerService->recordStockPrices($event->data, $event->symbol);
    }
}
