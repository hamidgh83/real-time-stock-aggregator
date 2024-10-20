<?php

namespace App\Http\Controllers;

use App\Jobs\FetchStockData;
use Illuminate\Support\Facades\Artisan;

class FetchStockDataController extends Controller
{
    public function __construct(
        protected FetchStockData $job
    ) {}

    public function __invoke()
    {
        return view('data_seed');
    }

    public function run()
    {
        Artisan::call('db:seed', ['--class' => 'StockSymbolsSeeder']);
        $this->job->handle();

        return response()->redirectToRoute('home');
    }
}
