<?php

namespace App\Http\Controllers;

use App\Http\Services\ReportService;
use Illuminate\Http\Request;

class GetStockReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function __invoke(Request $request)
    {
        $collection = $this->reportService->getStockReport(5, $request->get('page', 1));

        return view('reports.index', [
            'records' => $collection,
        ]);
    }
}
