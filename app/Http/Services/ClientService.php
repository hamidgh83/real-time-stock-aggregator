<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ClientService
{
    public function __construct(
        protected Client $httpClient,
        protected CacheManager $cache
    ) {}

    /**
     * Retrieves stock data for the given symbol and interval.
     *
     * This method first checks the cache for the requested data. If the data is not
     * found in the cache, it makes an API request to AlphaVantage to fetch the
     * stock data and stores the result in the cache for the specified interval.
     *
     * @param string $symbol   the stock symbol to retrieve data for
     * @param int    $interval the interval in minutes for the stock data (default is 5 minutes)
     *
     * @return Collection a collection of stock data for the given symbol and interval
     */
    public function getStockData(string $symbol, int $interval = 5): Collection
    {
        // Define the cache key based on the symbol and interval
        $cacheKey = 'stock_intraday_' . md5($symbol . $interval);

        // Try to get the data from cache or make an API request
        return $this->cache->remember($cacheKey, $interval, function () use ($symbol, $interval) {
            $queryString = $this->buildQueryString([
                'function' => 'TIME_SERIES_INTRADAY',
                'symbol'   => $symbol,
                'interval' => $interval . 'min',
            ]);

            try {
                $response = $this->httpClient->get('', $queryString);
                $data     = json_decode($response->getBody(), true);

                throw_if(JSON_ERROR_NONE !== json_last_error(), new \Exception('Could not extract data from the API response.'));
                throw_if(isset($data['Information']), new \Exception($data['Information'] ?? 'Invalid API response.'));
            } catch (\Throwable $th) {
                Log::error('AlphaVantage API Error: ' . $th->getMessage());

                collect();
            }

            return collect($data['Time Series (' . $interval . 'min)'] ?? []);
        });
    }

    private function buildQueryString(array $params): array
    {
        return [RequestOptions::QUERY => [
            'apikey' => config('alpha_vantage.apiKey'),
        ] + $params];
    }
}
