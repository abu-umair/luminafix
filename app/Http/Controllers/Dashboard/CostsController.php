<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cost;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Carbon;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostsController extends Controller
{
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connectdatabase();
        $this->auth = \App\Services\FirebaseService::connectauth();
        $this->connect = \App\Services\FirebaseService::connect();
    }
    /**
     * Show details of all created costs/expenses
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $check =  Cost::first();
        if ($check) {
            $least_year = (int) (new Carbon(
                Cost::orderBy('billing_month')
                    ->first()
                    ->billing_month
            )
            )->format('Y');
    
            $cost = Cost::filter(request(['month', 'year']))
                ->orderBy('billing_month', 'desc')
                ->paginate(12)
                ->withQueryString();
        } else {
            $cost = null;
            $least_year = null;

        }

        return view('dashboard.costs', [
            'costs'       => $cost,
            // 'costs'       => Cost::orderBy('billing_month', 'desc')->paginate(12),

            
            // 'invoices'    => $invoices,
            'years'       => (int) date('Y') - $least_year,
        ]);
    }

    /**
     * Validate cost data and create new record in database
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'billing_month' => ['required', 'date_format:Y-m-d'],
            'amount'        => ['required', 'numeric', 'min:1', 'max:4294967295'],
            'balance'       => ['required', 'numeric', 'min:1', 'max:4294967295'],
        ]);

        [$year, $month, $date] = explode('-', $validated['billing_month']);

        // Can only add costs upto current month!
        if (($year > (int) date('Y')) || (($year == (int) date('Y')) && ($month > (int) date('m'))))
            return redirect()
                ->back()
                ->with('message', 'Cost for upcoming months cannot be created in advance!');

        $expenseExists = Cost::query()
            ->whereYear('billing_month', $year)
            ->whereMonth('billing_month', $month)
            ->first();
        
            
        
        // Only one cost per month!
        if ($expenseExists)
            return redirect()
                ->back()
                ->with('message', 'Cost for selected month already exists!');
                
        
         Cost::create([
            'billing_month' => sprintf('%s-%s-%s', $year, $month, $date),
            'amount'        => $validated['amount'],
            'balance'       => $validated['balance']
        ]);

        
        // store fee index firestore
        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $conecttable=  $newDatabase->collection('feeindex');
        $conecttable_estimate=  $newDatabase->collection('estimatefeeindex');

        // jika bulan jan || feb || march, maka yg di update fee indexnya dr bulan apr - dec
        if ($month == 01 || $month == 02 || $month == 03) {
            for ($i = 4; $i <= 12; $i+=1) {
                // estimasi
                try{
                    $currentCost = new Cost([
                        'billing_month' => sprintf('%s-%s-01', $year, $i)
                    ]);
                    
                    $costEstimate = $currentCost->calculate();
                    $bulan_depan = $currentCost->billing_month->format('m');
                    if ($bulan_depan > 12) {
                        $bulan_depan = 1;
                    }
                    $tahun = $currentCost->billing_month->format('Y');
                    // dd($currentCost);
                    $update = [
                        // 'date'           => $currentCost->billing_month->format('Y-m-d'),
                        'date'           => $tahun.'-'.$bulan_depan,
                        'fee-index'      => $costEstimate['calculations']['index'],
                    ];
                    $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                } catch (\Throwable $e) { 
                    // $conecttable_estimate=  $newDatabase->collection('estimatefeeindex');
                    // $costEstimate = $currentCost->calculate();
                    $tahun = $currentCost->billing_month->format('Y');
                    $bulan_depan = $currentCost->billing_month->format('m');
                    if ($bulan_depan > 12) {
                        $bulan_depan = 1;
                    }
                    $update = [
                        'date'           => $tahun.'-'.$bulan_depan,
                        'fee-index'      => null,
                    ];
                    $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                }

                // update fee index bulan apr
                $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();
                if ($cost1) {
                    try{
                        $cal = $cost1->calculate();
                        $update = [
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index'      => $cal['calculations']['index'],
                        ];
                        $testRef =$conecttable->document($cost1->id);
                        // update firestore merge
                        $testRef->set($update, [
                            'merge' => true
                        ]);
                    } catch (\Throwable $e) { 
                        $testRef =$conecttable->document($cost1->id);
                        $testRef->set([
                            // 'id'   => $cost->id,
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index'      => null,
                        ]);
                        
                    }
                }
            }
        }
        
        // jika bulan apr - decem, maka yg di update fee indexnya  sampai bulan 3 bulan kedepan
        elseif ($month > 03 && $month <= 12 ) {
            for ($i = $month; $i <= 12; $i+=1) {

            // estimasi
            try{
                $currentCost = new Cost([
                    'billing_month' => sprintf('%s-%s-01', $year, $i + 1)
                ]);
                
                $costEstimate = $currentCost->calculate();
                $bulan_depan = $currentCost->billing_month->format('m');
                if ($bulan_depan > 12) {
                    $bulan_depan = 1;
                }
                $tahun = $currentCost->billing_month->format('Y');
                // dd($currentCost);
                $update = [
                    // 'date'           => $currentCost->billing_month->format('Y-m-d'),
                    'date'           => $tahun.'-'.$bulan_depan,
                    'fee-index'      => $costEstimate['calculations']['index'],
                ];
                $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                // update firestore merge
                $testRef->set($update, [
                    'merge' => true
                ]);
            } catch (\Throwable $e) { 
                $tahun = $currentCost->billing_month->format('Y');
                $bulan_depan = $currentCost->billing_month->format('m');
                if ($bulan_depan > 12) {
                    $bulan_depan = 1;
                }
                $update = [
                    'date'           => $tahun.'-'.$bulan_depan,
                    'fee-index'      => null,
                ];
                $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                // update firestore merge
                $testRef->set($update, [
                    'merge' => true
                ]);
            }

            $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();
            if ($cost1) {
                    try{
                        $cal = $cost1->calculate();
                        $update = [
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index'      => $cal['calculations']['index'],
                        ];
                        $testRef =$conecttable->document($cost1->id);
                        // update firestore merge
                        $testRef->set($update, [
                            'merge' => true
                        ]);
                    } catch (\Throwable $e) { 
                        $testRef =$conecttable->document($cost1->id);
                        $testRef->set([
                            // 'id'   => $cost->id,
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index' => null,
                        ]);
                        
                    }
                }
            }
        } 

        
        

        return redirect()
            ->route('dashboard.costs')
            ->with('created', 'New expense was created successfully!');
    }

    /**
     * Validate cost data and update data in database
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\Cost  $cost
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Cost $cost): RedirectResponse
    {
        $validated = $request->validate([
            'amount'  => ['required', 'numeric', 'min:1', 'max:4294967295'],
            'balance' => ['required', 'numeric', 'min:1', 'max:4294967295'],
        ]);
        

        $cost->update([
            'amount'  => $validated['amount'],
            'balance' => $validated['balance']
        ]);

        // update firestore
        $month = $cost->billing_month->format('m');
        $year = $cost->billing_month->format('Y');

        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $conecttable=  $newDatabase->collection('feeindex');
        $conecttable_estimate=  $newDatabase->collection('estimatefeeindex');

        
        // jika bulan jan || feb || march, maka yg di update fee indexnya dr bulan apr - dec
        if ($month == 01 || $month == 02 || $month == 03) {
            for ($i = 4; $i <= 12; $i+=1) {
                 // estimasi
                 try{
                    $currentCost = new Cost([
                        'billing_month' => sprintf('%s-%s-01', $year, $i)
                    ]);
                    
                    $costEstimate = $currentCost->calculate();
                    $bulan_depan = $currentCost->billing_month->format('m');
                    if ($bulan_depan > 12) {
                        $bulan_depan = 1;
                    }
                    $tahun = $currentCost->billing_month->format('Y');
                    // dd($currentCost);
                    $update = [
                        // 'date'           => $currentCost->billing_month->format('Y-m-d'),
                        'date'           => $tahun.'-'.$bulan_depan,
                        'fee-index'      => $costEstimate['calculations']['index'],
                    ];
                    $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                } catch (\Throwable $e) { 
                    $tahun = $currentCost->billing_month->format('Y');
                    $bulan_depan = $currentCost->billing_month->format('m');
                    if ($bulan_depan > 12) {
                        $bulan_depan = 1;
                    }
                    $update = [
                        'date'           => $tahun.'-'.$bulan_depan,
                        'fee-index'      => null,
                    ];
                    $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                }

                // update fee index bulan apr
                $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();
                if ($cost1) {
                    try{
                        $cal = $cost1->calculate();
                        $update = [
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index'      => $cal['calculations']['index'],
                        ];
                        $testRef =$conecttable->document($cost1->id);
                        // update firestore merge
                        $testRef->set($update, [
                            'merge' => true
                        ]);
                    } catch (\Throwable $e) { 
                        $testRef =$conecttable->document($cost1->id);
                        $testRef->set([
                            // 'id'   => $cost->id,
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index'      => null,
                        ]);
                        
                    }
                }
            }
        }
        
        // jika bulan apr - decem, maka yg di update fee indexnya  sampai bulan 3 bulan kedepan
        elseif ($month > 03 && $month <= 12 ) {
            for ($i = $month; $i <= 12; $i+=1) {
            // estimasi
            try{
                $currentCost = new Cost([
                    'billing_month' => sprintf('%s-%s-01', $year, $i + 1)
                ]);
                
                $costEstimate = $currentCost->calculate();
                $bulan_depan = $currentCost->billing_month->format('m');
                if ($bulan_depan > 12) {
                    $bulan_depan = 1;
                }
                $tahun = $currentCost->billing_month->format('Y');
                // dd($currentCost);
                $update = [
                    // 'date'           => $currentCost->billing_month->format('Y-m-d'),
                    'date'           => $tahun.'-'.$bulan_depan,
                    'fee-index'      => $costEstimate['calculations']['index'],
                ];
                $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                // update firestore merge
                $testRef->set($update, [
                    'merge' => true
                ]);
            } catch (\Throwable $e) { 
                $tahun = $currentCost->billing_month->format('Y');
                $bulan_depan = $currentCost->billing_month->format('m');
                if ($bulan_depan > 12) {
                    $bulan_depan = 1;
                }
                $update = [
                    'date'           => $tahun.'-'.$bulan_depan,
                    'fee-index'      => null,
                ];
                $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                // update firestore merge
                $testRef->set($update, [
                    'merge' => true
                ]);
            }

            //Update fee index 
            $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();
                if ($cost1) {
                    try{
                        $cal = $cost1->calculate();
                        $update = [
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index'      => $cal['calculations']['index'],
                        ];
                        $testRef =$conecttable->document($cost1->id);
                        // update firestore merge
                        $testRef->set($update, [
                            'merge' => true
                        ]);
                    } catch (\Throwable $e) { 
                        $testRef =$conecttable->document($cost1->id);
                        $testRef->set([
                            // 'id'   => $cost->id,
                            'date'           => $cost1->billing_month->format('Y-m-d'),
                            'fee-index' => null,
                        ]);
                        
                    }
                }
            }
        } 


        return redirect()
            ->route('dashboard.costs')
            ->with('updated', 'The expense was updated successfully!');
    }


    /**
     * Delete the specified cost's record from database
     *
     * @param \App\Models\Cost  $cost
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cost $cost): RedirectResponse
    {
        

        $year = date('Y', strtotime($cost->billing_month));
        // delete firestore feeindex
        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $conecttable=  $newDatabase->collection('feeindex');
        $conecttable_estimate=  $newDatabase->collection('estimatefeeindex');
        $testRef = $conecttable->document($cost->id)->delete();

        $cost->delete();

        // update firestore
        for ($i = 4; $i <= 12; $i+=1) {
            // estimasi
            try{
                $currentCost = new Cost([
                    'billing_month' => sprintf('%s-%s-01', $year, $i)
                ]);
                
                $costEstimate = $currentCost->calculate();
                $bulan_depan = $currentCost->billing_month->format('m');
                if ($bulan_depan > 12) {
                    $bulan_depan = 1;
                }
                $tahun = $currentCost->billing_month->format('Y');
                
                $update = [
                    'date'           => $tahun.'-'.$bulan_depan,
                    'fee-index'      => $costEstimate['calculations']['index'],
                ];
                $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                // update firestore merge
                $testRef->set($update, [
                    'merge' => true
                ]);
            } catch (\Throwable $e) { 
                $tahun = $currentCost->billing_month->format('Y');
                $bulan_depan = $currentCost->billing_month->format('m');
                if ($bulan_depan > 12) {
                    $bulan_depan = 1;
                }
                $update = [
                    'date'           => $tahun.'-'.$bulan_depan,
                    'fee-index'      => null,
                ];
                $testRef =$conecttable_estimate->document($tahun.'-'.$bulan_depan);
                // update firestore merge
                $testRef->set($update, [
                    'merge' => true
                ]);
            }

            // update fee index bulan apr
            $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();
            if ($cost1) {
                try{
                    $cal = $cost1->calculate();
                    $update = [
                        'fee-index'      => $cal['calculations']['index'],
                    ];
                    $testRef =$conecttable->document($cost1->id);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                } catch (\Throwable $e) {
                    $testRef =$conecttable->document($cost1->id);
                    $testRef->set([
                        'date'           => $cost1->billing_month->format('Y-m-d'),
                        'fee-index' => null,
                    ]);
                }
            }
            
        }



        return redirect()
            ->route('dashboard.costs')
            ->with('deleted', 'New expense was deleted successfully!');
    }
}
