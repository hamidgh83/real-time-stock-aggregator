<?php

namespace App\Http\Services;

use App\Models\StockPrice;
use App\Models\StockSymbol;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockManagerService
{
    public function __construct() {}

    public function getSymbols(): EloquentCollection
    {
        // As the number of symbols is small, we can use the all() method to retrieve all symbols.
        // Pagination is required for a larger number of symbols.
        return StockSymbol::all();
    }

    /**
     * Records a collection of stock prices for the given symbol.
     *
     * This method will upsert the provided stock price records into the database,
     * using the symbol and timestamp as the unique identifier. If a record already
     * exists for the given symbol and timestamp, it will be updated with the new
     * price data.
     *
     * @param Collection $records the collection of stock price records to be recorded
     * @param string     $symbol  the stock symbol associated with the price records
     *
     * @throws \Throwable if there is an error upserting the stock price records
     */
    public function recordStockPrices(Collection $records, string $symbol)
    {
        DB::beginTransaction();

        try {
            StockPrice::upsert(
                $this->transformData($records, $symbol)->toArray(),
                ['symbol', 'timestamp'],
                ['open', 'high', 'low', 'close', 'volume']
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to upsert stock prices.', [
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function transformData(Collection $data, string $symbol): Collection
    {
        return $data->map(function ($record, $timestamp) use ($symbol) {
            $transformedData = [];
            foreach ($record as $key => $value) {
                $newKey                   = preg_replace('/^\d+\.\s*/', '', $key);
                $transformedData[$newKey] = $value;
            }

            return $transformedData + ['timestamp' => $timestamp, 'symbol' => $symbol];
        });
    }
}
