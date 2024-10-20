<?php

namespace App\Http\Services;

use App\Models\StockPrice;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportService
{
    public function getStockReport(int $interval = 5, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $paginator = $this->getReport($interval, $page, $perPage);

        $mapped = $paginator->getCollection()->groupBy('symbol')->map(function ($group) {
            $firstRecord  = $group->where('timestamp', $group->min('timestamp'))->first();
            $lastRecord   = $group->where('timestamp', $group->max('timestamp'))->first();
            $initialPrice = $firstRecord->open;
            $finalPrice   = $lastRecord->close;

            return [
                'open'   => $initialPrice,
                'close'  => $finalPrice,
                'symbol' => $firstRecord->symbol,
                'name'   => $firstRecord->symbolDetails->description,
                'high'   => $lastRecord->high,
                'low'    => $lastRecord->low,
                'volume' => $lastRecord->volume,
                'change' => $this->calculatePercentageChange($initialPrice, $finalPrice),
            ];
        });

        return $paginator->setCollection($mapped);
    }

    protected function getReport(int $interval, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $subQuery = StockPrice::select('symbol')
            ->selectRaw('MAX(timestamp) AS max_timestamp, MIN(timestamp) AS min_timestamp')
            ->whereRaw('timestamp BETWEEN (
            SELECT MAX(timestamp) - INTERVAL ? MINUTE
            FROM stock_prices AS sp1
            WHERE sp1.symbol = stock_prices.symbol
        )
        AND (
            SELECT MAX(timestamp)
            FROM stock_prices AS sp2
            WHERE sp2.symbol = stock_prices.symbol
        )', [$interval])
            ->groupBy('symbol')
        ;

        return StockPrice::from('stock_prices as sp1')
            ->joinSub($subQuery, 'subq', function ($join) {
                $join->on('subq.symbol', '=', 'sp1.symbol');
            })
            ->where(function ($query) {
                $query->whereColumn('sp1.timestamp', 'subq.max_timestamp')
                    ->orWhereColumn('sp1.timestamp', 'subq.min_timestamp')
                ;
            })
            ->with('symbolDetails')
            ->paginate($perPage * 2, '*', 'page', $page)
        ;
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
