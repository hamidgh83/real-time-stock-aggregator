<?php

namespace App\Http\Controllers;

use App\Http\Services\ReportService;

class GetStockReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function __invoke()
    {
        return view('reports.index');
    }
}
