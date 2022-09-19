<?php

namespace App\Http\Controllers\API\v1;

use App\Helpers\JSON;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use Illuminate\Http\Request;

class InitialDataController extends Controller
{
    /**
     * Return JSON response for this API endpoint
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $workload = (int) $user->workload;
        $progress = $user->projectProgress;

        $fee_index = [
            'previous' => 0,
            'current'  => 0,
            'next'     => 0
        ];

        // Get the cost for current month
        $cost = Cost::filter(['month' => (int) date('m'), 'year' => (int) date('Y')])->first();

        // Cost for current month does not exists!
        if (!$cost) {
            // Get cost for previous month
            $cost = Cost::filter([
                'month' => ((int) date('m')) - 1,
                'year' => (int) date('Y')
            ])->first();

            // Cost for previous month exists, we can calculate current month's cost
            if ($cost)
                $cost = Cost::currentMonth(); // Create a dummy record
        }

        // Cost for current/previous month was found and can perform
        // calculations on it
        if ($cost)
            $fee_index = [
                'previous' => $cost->lastMonth->calculate()['calculations']['index'],
                'current'  => $cost->calculate()['calculations']['index'],
                'next'     => $cost->estimateNext()['calculations']['index']
            ];

        return JSON::success(data: compact('workload', 'progress', 'fee_index'));
    }
}
