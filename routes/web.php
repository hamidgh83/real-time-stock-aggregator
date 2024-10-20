<?php

use App\Http\Controllers\FetchStockDataController;
use App\Http\Controllers\GetStockReportController;
use App\Http\Controllers\GetSymbolPriceCHangesControler;
use Illuminate\Support\Facades\Route;

Route::get('/', GetStockReportController::class)->name('home');
Route::get('/seed', FetchStockDataController::class)->name('seed');
Route::get('/seed/run', FetchStockDataController::class . '@run')->name('seed.run');

Route::group(['prefix' => 'api', 'middleware' => 'api-group'], function () {
    Route::get('/stock/{symbol}/prices', GetSymbolPriceCHangesControler::class)->name('stock-data');
});
