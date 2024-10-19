<?php

namespace Tests\Unit\Jobs;

use App\Events\UpdateStockData;
use App\Http\Services\ClientService;
use App\Http\Services\StockManagerService;
use App\Jobs\FetchStockData;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FetchStockDataTest extends TestCase
{
    use RefreshDatabase;

    protected $mockClientService;
    protected $mockStockManagerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockServices();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testItFetchesStockDataAndDispatchesUpdateEvent()
    {
        Event::fake();

        $symbols = new EloquentCollection([
            (object) ['name' => 'IBM'],
        ]);

        $stockData = [
            'Meta Data' => [
                '1. Information'    => 'Intraday (5min) open, high, low, close prices and volume',
                '2. Symbol'         => 'IBM',
                '3. Last Refreshed' => '2024-10-18 19:55:00',
                '4. Interval'       => '5min',
                '5. Output Size'    => 'Full size',
                '6. Time Zone'      => 'US/Eastern',
            ],
            'Time Series (5min)' => [
                '2024-10-18 19:55:00' => [
                    '1. open'   => '232.3100',
                    '2. high'   => '232.3100',
                    '3. low'    => '232.3100',
                    '4. close'  => '232.3100',
                    '5. volume' => '6',
                ],
                '2024-10-18 19:50:00' => [
                    '1. open'   => '232.3000',
                    '2. high'   => '232.4000',
                    '3. low'    => '232.3000',
                    '4. close'  => '232.4000',
                    '5. volume' => '207',
                ],
            ],
        ];

        // Mocking the stock manager service
        $this->mockStockManagerService->shouldReceive('getSymbols')
            ->once()
            ->andReturn($symbols)
        ;

        // Mocking the client service
        $this->mockClientService->shouldReceive('getStockData')
            ->with('IBM', config('alpha_vantage.dataInterval'))
            ->once()
            ->andReturn(collect($stockData['Time Series (5min)']))
        ;

        // Execute the job
        $job = new FetchStockData($this->mockClientService, $this->mockStockManagerService);
        $job->handle();

        // Assert the event was dispatched with correct data count
        Event::assertDispatched(UpdateStockData::class, function ($event) {
            return 2 === $event->data->count(); // Check that 2 records were passed
        });
    }

    public function testItLogsErrorWhenFetchingFails()
    {
        Event::fake();
        $this->mockServices();

        $symbols = new EloquentCollection([(object) ['name' => 'AAPL']]);

        // Mocking the stock manager service
        $this->mockStockManagerService->shouldReceive('getSymbols')
            ->once()
            ->andReturn($symbols)
        ;

        // Mocking the client service to return an empty collection
        $this->mockClientService->shouldReceive('getStockData')
            ->with('AAPL', config('alpha_vantage.dataInterval'))
            ->once()
            ->andReturn(collect([]))
        ;

        // Execute the job
        $job = new FetchStockData($this->mockClientService, $this->mockStockManagerService);
        $job->handle();

        $this->assertLogContains('No data found for symbol "AAPL".');
    }

    protected function assertLogContains(string $message)
    {
        $logFile = storage_path('logs/laravel.log');
        $this->assertStringContainsString($message, file_get_contents($logFile));
    }

    protected function mockServices()
    {
        $this->mockClientService       = \Mockery::mock(ClientService::class);
        $this->mockStockManagerService = \Mockery::mock(StockManagerService::class);
        $this->app->instance(ClientService::class, $this->mockClientService);
        $this->app->instance(StockManagerService::class, $this->mockStockManagerService);
    }
}
