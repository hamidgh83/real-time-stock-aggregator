<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPriceChangesRequest;
use App\Http\Resources\PriceChangesResource;
use App\Http\Services\ReportService;
use App\Models\StockSymbol;

class GetSymbolPriceChangesControler extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function __invoke(GetPriceChangesRequest $request, StockSymbol $symbol)
    {
        $validated = $request->validated();

        return PriceChangesResource::make(
            $this->reportService->getStockReport(
                $validated['data_interval'] ?? 5,
                $validated['page']          ?? 1,
                $validated['per_page']      ?? 10,
            )
        );
    }
}
