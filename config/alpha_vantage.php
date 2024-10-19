<?php

return [
    'url'          => env('ALPHA_VANTAGE_API_URL', 'https://www.alphavantage.co/query'),
    'apiKey'       => env('ALPHA_VANTAGE_API_KEY', 'demo'),
    'function'     => env('ALPHA_VANTAGE_FUNCTION', 'TIME_SERIES_INTRADAY'),
    'dataInterval' => env('ALPHA_VANTAGE_DATA_INTERVAL_MIN', 5),
];
