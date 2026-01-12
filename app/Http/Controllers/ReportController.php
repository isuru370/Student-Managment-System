<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{

    protected $reportService;
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }


    public function indexPage()
    {
        return view('reports.index');
    }

    public function downloadDailyReportPdf($day)
    {
        $report = $this->reportService->generateAllDailyPaymentsReport($day);

        $pdf = Pdf::loadView('reports.daily_pdf', compact('report'));

        return $pdf->download('daily-report-' . $day . '.pdf');
    }
}
