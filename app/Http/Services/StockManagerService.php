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
        return StockSymbol::all();
    }

    public function recordStockPrices(Collection $records, string $symbol)
    {
        DB::beginTransaction();

        $data = $records->map(function ($record, $timestamp) use ($symbol) {
            $transformedData = [];
            foreach ($record as $key => $value) {
                $newKey                   = preg_replace('/^\d+\.\s*/', '', $key);
                $transformedData[$newKey] = $value;
            }

            return $transformedData + ['timestamp' => $timestamp, 'symbol' => $symbol];
        });

        try {
            StockPrice::upsert(
                $data->toArray(),
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
}
