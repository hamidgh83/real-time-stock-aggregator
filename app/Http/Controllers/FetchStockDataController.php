<?php

namespace App\Http\Controllers;

use App\Jobs\FetchStockData;

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
        $this->job->handle();

        return response()->redirectToRoute('home');
    }
}
