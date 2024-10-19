<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StockSymbolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/stock_symbols_data.json');
        $jsonData = File::get($jsonPath);
        $data     = json_decode($jsonData, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->command->error('Error parsing JSON file: ' . json_last_error_msg());

            return;
        }

        $totalUpdates = 0;
        $collection   = collect($data)->map(function ($item) {
            return [
                'name'         => $item['symbol'],
                'description'  => $item['name'],
                'type'         => $item['type'],
                'market_open'  => $item['marketOpen'],
                'market_close' => $item['marketClose'],
                'currency'     => $item['currency'],
                'region'       => $item['region'],
                'timezone'     => $item['timezone'],
            ];
        })->each(function ($record) use (&$totalUpdates) {
            $totalUpdates += (int) DB::table('stock_symbols')->updateOrInsert(
                ['name' => $record['name'], 'region' => $record['region']],
                $record
            );
        });

        $this->command->info(sprintf('Total records synchronized with database: %d/%d', $totalUpdates, $collection->count()));
    }
}
