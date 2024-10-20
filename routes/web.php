<?php

use App\Http\Controllers\FetchStockDataController;
use App\Http\Controllers\GetStockReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', GetStockReportController::class)->name('home');
Route::get('/seed', FetchStockDataController::class)->name('seed');
Route::get('/seed/run', FetchStockDataController::class . '@run')->name('seed.run');
