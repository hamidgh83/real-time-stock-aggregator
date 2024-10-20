<?php

namespace App\Http\Services;

use App\Models\StockPrice;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportService
{
    /**
     * Get a paginated stock report for the specified time interval.
     *
     * @param int $interval the time interval in minutes to include in the report
     * @param int $page     the page number to retrieve
     * @param int $perPage  the number of records to return per page
     *
     * @return LengthAwarePaginator the paginated stock report
     */
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

    /**
     * Get a paginated report of stock prices for the specified time interval.
     *
     * This method retrieves a paginated list of stock prices for the specified time interval. It first
     * queries the database to find the minimum and maximum timestamps for each stock symbol within the
     * interval. It then joins this subquery to the main stock prices table to only return the records
     * at the start and end of the interval for each symbol. The results are then paginated and returned.
     *
     * @param int $interval the time interval in minutes to include in the report
     * @param int $page     the page number to retrieve
     * @param int $perPage  the number of records to return per page
     *
     * @return LengthAwarePaginator the paginated stock report
     */
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
     * Calculates the percentage change between two float values.
     *
     * @param float $previous the previous value
     * @param float $current  the current value
     *
     * @return float the percentage change between the previous and current values
     */
    private function calculatePercentageChange(float $previous, float $current): float
    {
        if (0 == $previous) {
            return 0; // Avoid division by zero
        }

        return (($current - $previous) / $previous) * 100;
    }
}
