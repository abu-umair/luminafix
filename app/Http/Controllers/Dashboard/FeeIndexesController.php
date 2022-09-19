<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\Cost;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class FeeIndexesController extends Controller
{
    /**
     * Show the detailed data table for entire year
     */
    public function index(Request $request): View
    {
        // Validate month and year query parameters
        $request->validate([
            'month' => ['required_with:year', 'numeric', 'min:4', 'max:12'],
            'year'  => ['required_with:month', 'numeric', 'min:1900', 'max:3000']
        ]);

        $year = (int) ($request->input('year') ?: date('Y'));
        $month = (int) ($request->input('month') ?: date('m'));

        // Get the least selectable year for filtering (UI)
        $leastYear = (int) Cost::orderBy('billing_month')->first()?->year ?: (int) date('Y');
        
        // User is trying to view calculations for next year
        if ($year > (int) date('Y'))
            return view('dashboard.fee-index')
                ->with([
                    'error' => 'Next year\'s fees will be calculated in corresponding year only!',
                    'leastYear' => $leastYear
                ]);

        // Get the requested cost
        $cost = Cost::filter(compact('month', 'year'))->first();
        // dd($cost);

        // User is trying to view estimates for a far future month
        if (!$cost && $year === (int) date('Y') && $month > ((int) date('m')) + 2)
            //jika cost false, dan tahun sama dengan tahun, dan bulan lebih dari bulan yang ditambah 2 bulan kedepan 
            return view('dashboard.fee-index')
                ->with([
                    'error' => 'Fee index can be estimated for upcoming month only!',
                    'leastYear' => $leastYear
                ]);

        // User is trying to view estimates for next month
        if (!$cost && $year === (int) date('Y') && $month === ((int) date('m')) + 1) {
            //jika cost false, dan tahun sama dengan tahun, dan bulan sama dengan dari bulan yang ditambah 1 bulan kedepan

            // Get cost for current month
            $cost = Cost::filter(['month' => (int) date('m'), 'year' => (int) date('Y')])
                ->first();

            // Cost for current month also not exists
            // Create a dummy cost for current month
            $currentCost = Cost::currentMonth();
            if (!$cost) {
                // Cost for last month is also not entered, we cannot perform calculations!
                if (!$currentCost->lastMonth)
                    return view('dashboard.fee-index')
                        ->with([
                            'error' => sprintf(
                                'Cannot make estimates for %s please at least enter costs until %s!',
                                (new Carbon(sprintf('%s-%s-01', $year, $month)))->format('F Y'),
                                (new Carbon(sprintf('%s-%s-01', $year, $currentCost->month - 1)))->format('F Y')
                            ),
                            'leastYear' => $leastYear
                        ]);
            }

            try {
                $calculations = $currentCost->estimateNext();
            } catch (Exception $error) {
                return view('dashboard.fee-index', [
                    'leastYear' => $leastYear,
                    'error' => $error->getMessage()
                ]);
            }

            // dd(...array_values($calculations));
            // dd($calculations);
            return view('dashboard.fee-index', [
                'leastYear' => $leastYear,
                'calculations' => $calculations
                // array_fill(...$calculations)
                // ...array_values($calculations),
                // array(...$calculations),
                // ...$calculations
                
            ]);
        }

        // User may be trying to view fee index for current or previous month(s)
        if (!$cost) {
            // Get the cost for previous month
            $previousCost = Cost::filter(['year' => $year, 'month' => $month - 1])->first();
            if (!$previousCost)
                return view('dashboard.fee-index')
                    ->with([
                        'error' => sprintf(
                            'Please enter cost and balance upto %s first!',
                            (new Carbon(sprintf('%s-%s-01', $year, $month - 1)))->format('F Y')
                        ),
                        'leastYear' => $leastYear
                    ]);

            // The cost for previous month exist and we can perform calculations!
            $cost = new Cost([
                'billing_month' => sprintf('%s-%s-01', $year, $month)
            ]);
            // dd($cost);
        }


        try {
            
            // dd($cost);
            $calculations = $cost->calculate();
        } catch (Exception $error) {
            return view('dashboard.fee-index', [
                'leastYear' => $leastYear,
                'error' => $error->getMessage()
            ]);
        }
        
        // dd(...array_values($calculations));
        // dd($calculations);
        return view('dashboard.fee-index', [
            'leastYear' => $leastYear,
            'calculations' => $calculations
            // array_fill(...$calculations)
            // ...array_values($calculations),
            // array(...$calculations),
            // ...$calculations
            
        ]);
    }
}
