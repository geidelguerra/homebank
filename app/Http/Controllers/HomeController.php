<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request, ReportService $report)
    {
        return inertia('Home', [
            'incomeVsExpense' => $report->incomeVsExpense(
                now()->startOfYear(),
                now()
            )
        ]);
    }
}
