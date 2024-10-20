<?php

namespace Tests\Feature;

use App\Http\Controllers\GetSymbolPriceChangesControler;
use App\Models\StockPrice;
use App\Models\StockSymbol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class GetSymbolPriceChangesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/api/price-changes/{symbol?}', GetSymbolPriceChangesControler::class);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testItThrowsValidationErrorOnInvalidDataInterval()
    {
        $response = $this->postJson('/api/price-changes', [
            'data_interval' => 100, // Invalid data_interval
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['data_interval'])
        ;
    }

    public function testItThrowsValidationErrorOnInvalidPageNumber()
    {
        $response = $this->postJson('/api/price-changes', [
            'page' => 0, // Invalid page
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page'])
        ;
    }

    public function testItThrowsValidationErrorOnInvalidPerPage()
    {
        $response = $this->postJson('/api/price-changes', [
            'per_page' => 100, // Invalid per_page
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page'])
        ;
    }

    public function testItReturnsSuccessfulEmptyReponseWithEmptyDatabase()
    {
        $response = $this->postJson('/api/price-changes', [
            'data_interval' => 5,
            'page'          => 1,
            'per_page'      => 10,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(0, 'data')
        ;
    }

    public function testItReturnsSuccessfulPaginatedResponse()
    {
        $startTime = now();
        $symbols   = StockSymbol::factory()->count(5)->create();

        $symbols->each(function ($symbol) use ($startTime) {
            collect(range(1, 5))->each(function ($minute) use ($symbol, $startTime) {
                StockPrice::factory()
                    ->symbolWithSequentialTimestamps($symbol->name, $startTime->copy()->subMinutes($minute))
                    ->create()
                ;
            });
        });

        $response = $this->postJson('/api/price-changes', [
            'data_interval' => 5,
            'page'          => 1,
            'per_page'      => 10,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonStructure([
                'data' => [
                    'AAPL' => [
                        'open',
                        'close',
                        'symbol',
                        'name',
                        'high',
                        'low',
                        'volume',
                        'change',
                    ],
                    'GOOGL' => [
                        'open',
                        'close',
                        'symbol',
                        'name',
                        'high',
                        'low',
                        'volume',
                        'change',
                    ],
                    'NVIDA' => [
                        'open',
                        'close',
                        'symbol',
                        'name',
                        'high',
                        'low',
                        'volume',
                        'change',
                    ],
                    'MSFT' => [
                        'open',
                        'close',
                        'symbol',
                        'name',
                        'high',
                        'low',
                        'volume',
                        'change',
                    ],
                    'SONY' => [
                        'open',
                        'close',
                        'symbol',
                        'name',
                        'high',
                        'low',
                        'volume',
                        'change',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        ['url', 'label', 'active'],
                        ['url', 'label', 'active'],
                        ['url', 'label', 'active'],
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ])
        ;
    }
}
