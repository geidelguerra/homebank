<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\ReportService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request, ReportService $report)
    {
        $filteredCurrency = $request->string('filtered_currency', 'USD');

        return inertia('Home', [
            'incomeVsExpense' => function () use ($report, $filteredCurrency) {
                return $report->incomeVsExpense(
                    now()->startOfYear(),
                    now(),
                    $filteredCurrency
                );
            },
            'availableCurrencies' => function () {
                return Currency::query()->orderBy('code')->get();
            },
            'filteredCurrency' => $filteredCurrency
        ]);
    }
}
