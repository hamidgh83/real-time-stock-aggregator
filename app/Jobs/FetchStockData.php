<?php

namespace App\Jobs;

use App\Events\UpdateStockData;
use App\Http\Services\ClientService;
use App\Http\Services\StockManagerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchStockData implements ShouldQueue
{
    use Queueable;

    protected const CHUNK_SIZE = 5;

    protected int $dataInterval = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected ClientService $clientService,
        protected StockManagerService $stockManagerService
    ) {
        $this->dataInterval = config('alpha_vantage.dataInterval');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->stockManagerService->getSymbols()->chunk(self::CHUNK_SIZE)->each(function ($symbols) {
            $symbols->each(function ($symbol) {
                try {
                    // Fetch stock data for each symbol
                    $stockData = $this->clientService->getStockData($symbol->name, $this->dataInterval);

                    if (0 == $stockData->count()) {
                        Log::error(sprintf('No data found for symbol "%s".', $symbol->name));

                        return;
                    }

                    // Enqueue an event to update the stock data
                    event(new UpdateStockData($symbol->name, $stockData));
                } catch (\Throwable $e) {
                    Log::error(sprintf('Error fetching data for symbol "%s": %s', $symbol->name, $e->getMessage()));
                }

                // Freeing up resources
                unset($stockData);
            });

            // Clear memory after processing a chunk
            gc_collect_cycles();
        });
    }
}
