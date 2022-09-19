<?php

namespace App\Traits;

use Exception;
use App\Models\Setting;
use Illuminate\Support\Carbon;

trait CalculatesFeeIndex
{
    /**
     * Parses current cost's `billing_month` field into month and year.
     *
     * @return array<string, int>
     */
    public function getMonthAndYear(): array
    {
        $monthAndYear = explode(
            '-',
            (new Carbon($this->billing_month))
                ->format('m-Y')
        );

        return [
            'month' => (int) $monthAndYear[0],
            'year'  => (int) $monthAndYear[1]
        ];
    }

    public function calculateFirst(): array
    {
        // Get costs for JAN, FEB and MAR
        $previous = $this->previousMonths;

        // Records for JAN, FEB and MAR must be entered in order to calculate
        if ($previous->count() < 3)
            throw new Exception(
                sprintf(
                    'Please enter costs and invoices for %s, %s and %s!',
                    sprintf('January %s', date('Y')),
                    sprintf('February %s', date('Y')),
                    sprintf('March %s', date('Y')),
                )
            );

        // var(SPAN), var(SAFE), var(THETA)
        $var  = $this->getVariables();
        $inv  = $this->lastMonth->balance ?: 0;  // INV (end-of-month balance on MONTH-1)
        $cost = $this->lastMonth->amount ?: 0; // COST = (cost of MONTH-1)

        $avg = [
            'inv'  => $this->averageInv, // avg(INV) - Last 3 months
            'cost' => $this->averageCost, // avg(INV) - Last 3 months
        ];

        // var(FEE_THETA) x INV(MONTH-1), value of `FEE-THETA` field
        $fee_theta = $var['fee_theta'] * (int) $previous->get(2)->inv;

        // NETT = INV - COST
        $nett = $inv - $cost;

        /**
         *          {NETT      -  [ var(SAFE) x avg(COST) ]}
         * INDEX =  ________________________________________
         *          {FEE_THETA +  [ var(SPAN) x avg(COST) ]}
         */
        $index = round(
            (
                ($nett - ($var['safe'] * $avg['cost'])) /
                ($fee_theta + ($var['span'] * $avg['cost']))
            ) * 100,
            2,
            2
        );

        // FEE = INDEX <= 0 then 0 else INDEX * FEE_THETA
        $fee = $index <= 0 ? 0 : ($index / 100) * $fee_theta;

        return [
            'calculations'    => compact('inv', 'cost', 'fee_theta', 'fee', 'nett', 'index', 'avg'),
            'previous_months' => $previous,
            'current'         => $this,
            'estimated'       => false,
        ];
    }

    public function calculate(): array
    {
        // Calculations for April will be different
        if ($this->isFirstMonth)
            return $this->calculateFirst();

        // Get costs for last 3 months
        $previous = $this->previousMonths;
        
        // Records for last 3 months must be entered in order to calculate
        if ($previous->count() < 3) {
            throw new Exception(
                'Please enter costs and invoices for previous 3 months first!'
            );
        }

        // Get calculations for last month
        $lastMonth = $this->lastMonth->calculate()['calculations'];


        // var(SPAN), var(SAFE), var(THETA)
        $var  = $this->getVariables();

        // INV = (end-of-month balance of MONTH-1)
        $inv  = $this->lastMonth->balance ?: 0;

        $cost = $this->lastMonth->amount ?: 0; // COST = COST(MONTH-1)

        $avg = [
            'inv'  => $this->averageInv, // avg(INV) - Last 3 months
            'cost' => $this->averageCost, // avg(INV) - Last 3 months
        ];


        // FEE_THETA = var(FEE_THETA) x INV(MONTH-1)
        $fee_theta = $var['fee_theta'] * $this->lastMonth->inv;

        // NETT = INV - COST
        $nett = $inv - $cost;

        /**
         *          {NETT      -  [ var(SAFE) x avg(COST) ]}
         * INDEX =  ________________________________________
         *          {FEE_THETA +  [ var(SPAN) x avg(COST) ]}
         */
        $index = round(
            (
                ($nett - ($var['safe'] * $avg['cost'])) /
                ($fee_theta + ($var['span'] * $avg['cost']))
            ) * 100,
            2,
            2
        );

        // FEE = INDEX <= 0 then 0 else INDEX * FEE_THETA
        $fee = $index <= 0 ? 0 : ($index / 100) * $fee_theta;

        return [
            'calculations'    => compact('inv', 'cost', 'fee_theta', 'fee', 'nett', 'index', 'avg'),
            'previous_months' => $previous,
            'current'         => $this,
            'estimated'       => false,
        ];
    }

    public function estimateNext(): array
    {
        $currentCost = $this->calculate(); // Get calculations for current month
        $current = $currentCost['calculations']; // Get calculations for current month
        $var = $this->getVariables(); // Get the calculation variables

        // INV = avg[INV(MONTH-1)] + ( NETT(MONTH-1) - { INDEX(MONTH-1) * FEE_THETA(MONTH-1) } )
        $inv = round($current['avg']['inv'] + ($current['nett'] - (($current['index'] / 100) * $current['fee_theta'])));

        $cost = $current['avg']['cost']; // avg[COST(MONTH-1)]

        // FEE_THETA = avg[INV(MONTH-1)] * var(FEE_THETA)
        $fee_theta = floor($current['avg']['inv'] * $var['fee_theta']);

        $nett = $inv - $cost; // NETT = INV - COST

        // MONTH-4, MONTH-3, MONTH-2
        $previous = $currentCost['previous_months'];

        // MONTH-3, MONTH-2
        $avgPrevious = collect([$previous->get(1), $previous->get(2)]);


        $avg = [
            // INV(MONTH-3) + INV(MONTH-2) + avg(INV[MONTH-1])
            'inv'  => round(($previous->get(1)->inv + $previous->get(2)->inv + $current['avg']['inv']) / 3),
            // COST(MONTH-3) + COST(MONTH-2) + avg(COST[MONTH-1])
            'cost' => round(($previous->get(1)->cost + $previous->get(2)->cost + $current['avg']['cost']) / 3),
        ];

        /**
         * {NETT      - [ var(SAFE) x avg(COST) ]}
         * _______________________________________
         * {FEE_THETA + [ var(SPAN) x avg(COST) ]}
         */
        $index = round(
            (
                ($nett - ($var['safe'] * $avg['cost'])) /
                ($fee_theta + ($var['span'] * $avg['cost']))
            ) * 100,
            2,
            2
        );


        // FEE = INDEX <= 0 then 0 else INDEX * FEE_THETA
        $fee = round($index <= 0 ? 0 : ($index / 100) * $fee_theta);

        $avgPrevious->push($this);

        return [
            'calculations'    => compact('inv', 'cost', 'fee_theta', 'fee', 'nett', 'index', 'avg'),
            'last_month'      => $current,
            'previous_months' => $avgPrevious,
            'current'         => new self(['billing_month' => sprintf('%s-%s-01', $this->year, $this->month + 1)]),
            'estimated'       => true,
        ];
    }


    /**
     * Gets calculation variables (if present) or creates new with default
     * values and returns them in a numeric array.
     *
     * @return array<string>|array<string, string>
     */
    private function getVariables(?bool $assoc = true): array
    {
        // Get the calculation variables if saved or get default values and
        // persist them in database
        $setting = Setting::firstOrCreate(
            ['key' => 'variables'],
            // If not created yet then create using default values
            ['value' => config('lumina.variables.default')]
        );

        // If required then return in an associative array
        if ($assoc)
            return [
                'safe'      => (int)   $setting->value['safe'],
                'span'      => (int)   $setting->value['span'],
                'fee_theta' => (float) (round($setting->value['fee_theta']) / 100),
            ];

        return [
            (int) $setting->value['safe'],
            (int) $setting->value['span'],
            (float) (round($setting->value['fee_theta']) / 100),
        ];
    }
}
