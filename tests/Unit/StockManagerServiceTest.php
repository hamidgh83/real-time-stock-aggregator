<?php

namespace Tests\Unit\Services;

use App\Http\Services\StockManagerService;
use App\Models\StockPrice;
use App\Models\StockSymbol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StockManagerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockManagerService $stockManagerService;

    protected $mockModel;

    public function setUp(): void
    {
        parent::setUp();
        $this->stockManagerService = app()->make(StockManagerService::class);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testItFetchesAllStockSymbols()
    {
        $symbols = StockSymbol::factory()->count(2)->create();
        $result  = $this->stockManagerService->getSymbols();

        $this->assertEquals($symbols->toArray(), $result->toArray());
        $this->assertCount(2, $result);
    }

    public function testItRecordsStockPricesSuccessfully()
    {
        $records = $this->sampleApiResponse();

        $symbol = 'IBM';
        $this->stockManagerService->recordStockPrices($records, $symbol);

        $expectedRecords = [
            [
                'symbol'    => $symbol,
                'open'      => 232.3100,
                'high'      => 232.3100,
                'low'       => 232.3100,
                'close'     => 232.3100,
                'volume'    => 6,
                'timestamp' => '2024-10-18 19:55:00',
            ],
            [
                'symbol'    => $symbol,
                'open'      => 232.3000,
                'high'      => 232.4000,
                'low'       => 232.3000,
                'close'     => 232.4000,
                'volume'    => 207,
                'timestamp' => '2024-10-18 19:50:00',
            ],
        ];

        $this->assertDatabaseHas('stock_prices', $expectedRecords[0]);
        $this->assertDatabaseHas('stock_prices', $expectedRecords[1]);
    }

    public function testItLogsErrorWhenUpsertFailsAndRollsBackTransaction()
    {
        $records = $this->sampleApiResponse();
        $records->add(['open' => false]);   // Create bad data to make it throwing exception

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();

        $this->expectException(\Throwable::class);

        $this->stockManagerService->recordStockPrices($records, 'ABC');

        $this->assertDatabaseMissing('stock_prices', ['symbol' => 'ABC']);
    }


    protected function sampleApiResponse()
    {
        return new Collection([
            '2024-10-18 19:55:00' => ['1. open' => '232.3100', '2. high' => '232.3100', '3. low' => '232.3100', '4. close' => '232.3100', '5. volume' => '6'],
            '2024-10-18 19:50:00' => ['1. open' => '232.3000', '2. high' => '232.4000', '3. low' => '232.3000', '4. close' => '232.4000', '5. volume' => '207'],
        ]);
    }
}
