<?php

namespace App\Http\Services;

use App\Models\StockPrice;
use App\Models\StockSymbol;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Get the real-time stock prices and percentage changes.
     */
    public function getStockReport(): Collection
    {
        $symbols = StockSymbol::all();

        return $symbols->map(function ($symbol) {
            $latestPrices = StockPrice::where('symbol', $symbol->name)
                ->orderBy('timestamp', 'desc')
                ->take(2) // Fetch the latest 2 prices
                ->get()
            ;

            if ($latestPrices->count() < 2) {
                // Not enough data to calculate percentage change
                return [
                    'symbol'            => $symbol->name,
                    'latest_price'      => optional($latestPrices->first())->close,
                    'percentage_change' => null,
                ];
            }

            // Latest price
            $currentPrice = $latestPrices->first()->close;

            // Previous price (second latest)
            $previousPrice = $latestPrices->last()->close;

            $high   = $latestPrices->first()->high;
            $low    = $latestPrices->first()->low;
            $volume = $latestPrices->first()->volume;

            // Calculate percentage change
            $percentageChange = $this->calculatePercentageChange($previousPrice, $currentPrice);

            return [
                'symbol' => $symbol->name,
                'name'   => $symbol->description,
                'high'   => $high,
                'low'    => $low,
                'volume' => $volume,
                'close'  => $currentPrice,
                'change' => $percentageChange,
            ];
        });
    }

    /**
     * Calculate the percentage change between two prices.
     */
    private function calculatePercentageChange(float $previous, float $current): float
    {
        if (0 == $previous) {
            return 0; // Avoid division by zero
        }

        return (($current - $previous) / $previous) * 100;
    }
}
