<?php

use App\Http\Controllers\GetSymbolPriceChangesControler;
use Illuminate\Support\Facades\Route;

Route::post('/price-changes/{symbol?}', GetSymbolPriceChangesControler::class)->name('stock-data.symbol');
