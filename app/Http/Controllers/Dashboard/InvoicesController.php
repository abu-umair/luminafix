<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Cost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoicesController extends Controller
{
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connectdatabase();
        $this->auth = \App\Services\FirebaseService::connectauth();
        $this->connect = \App\Services\FirebaseService::connect();
    }
    /**
     * Show the invoices listing page
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $check =  Invoice::first();
        
        // dd($check);
        if ($check) {
            $least_year = (int) (new Carbon(
                Invoice::orderBy('paid_on')
                    ->first()
                    ->paid_on
            )
            )->format('Y');
    
            $invoices = Invoice::with('project')
                ->filter(request(['project_id', 'month', 'year']))
                ->orderBy('paid_on', 'desc')
                ->paginate(10)
                ->withQueryString();
            // dd($invoices);
        } else {
            $invoices = null;
            $least_year = null;

        }
        
        

        return view('dashboard.invoices', [
            'invoices'   => $invoices,
            'projects'   => Project::select(['id', 'name'])->get(),
            'years'      => (int) date('Y') - $least_year
        ]);
    }

    /**
     * Validate the invoice data and create new record in database.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_id' => ['required', 'numeric', Rule::exists('projects', 'id')],
            'paid_on'    => ['required', 'date_format:Y-m-d'],
            'amount'     => ['required', 'numeric', 'min:1', 'max:4294967295']
        ]);

        
        
        
        Invoice::create([
            'project_id' => $request->project_id,
            'paid_on'    => $request->paid_on,
            'amount'     => $request->amount
        ]);

        // update fee index firestore
        $month = date('m', strtotime($request->paid_on));
        $year = date('Y', strtotime($request->paid_on));

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
                    $cal = $cost1->calculate();
                    $update = [
                        'fee-index'      => $cal['calculations']['index'],
                    ];
                    $testRef =$conecttable->document($cost1->id);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
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

                // update fee index
                $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();            
                if ($cost1) {
                    $cal = $cost1->calculate();
                    $update = [
                        'fee-index'      => $cal['calculations']['index'],
                    ];
                    $testRef =$conecttable->document($cost1->id);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                }
            }
        } 
        
        // menambahkan 'totalinvoice' utk fungsi filter di project
        $project = Project::find($request->project_id);
        $project->totalinvoice += $request->amount;
        $project->save();

        return redirect()
            ->route('dashboard.invoices')
            ->with('created', 'The invoice was created successfully!');
    }

    /**
     * Validate the invoice data and update the record in database
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\Invoice  $invoice
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'paid_on' => ['required', 'date_format:Y-m-d'],
            'amount'  => ['required', 'numeric', 'min:1', 'max:4294967295']
        ]);

        // menambahkan 'totalinvoice' utk fungsi filter di project
        if ($request->amount != $invoice->amount) {
            $project = Project::find($invoice->project_id);
        // TODO: jika '$request->amount' ada, maka '$project->totalinvoice' dikurang 'invoice->amount' ditambah '$request->amount'
        $project->totalinvoice += $request->amount;
        $project->totalinvoice -= $invoice->amount;
        $project->save();
        }
        
        // menambahkan 'totalinvoice' utk fungsi filter di project

        $invoice->update([
            'paid_on'    => $request->paid_on,
            'amount'     => $request->amount
        ]);


        // update fee index firestore
        $month = date('m', strtotime($invoice->paid_on));
        $year = date('Y', strtotime($invoice->paid_on));

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
                    $cal = $cost1->calculate();
                    $update = [
                        'fee-index'      => $cal['calculations']['index'],
                    ];
                    $testRef =$conecttable->document($cost1->id);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
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

                // update fee index
                $cost1 = Cost::filter(['month' => $i, 'year' => $year])->first();            
                if ($cost1) {
                    $cal = $cost1->calculate();
                    $update = [
                        'fee-index'      => $cal['calculations']['index'],
                    ];
                    $testRef =$conecttable->document($cost1->id);
                    // update firestore merge
                    $testRef->set($update, [
                        'merge' => true
                    ]);
                }
            }
        } 

        return redirect()
            ->route('dashboard.invoices')
            ->with('updated', 'The invoice was updated successfully!');
    }

    /**
     * Delete the requested invoice from database
     *
     * @param \App\Models\Invoice  $invoice
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
       // utk pencarian
        $project = Project::find($invoice->project_id);
        $project->totalinvoice -= $invoice->amount;
        $project->save();


        $year = date('Y', strtotime($invoice->paid_on));
        $month = date('m', strtotime($invoice->paid_on));
        // delete firestore feeindex
        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $conecttable=  $newDatabase->collection('feeindex');
        $conecttable_estimate=  $newDatabase->collection('estimatefeeindex');
        $testRef = $conecttable->document($invoice->id)->delete();

        
        $invoice->delete();

        // update firestore
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

            // Update fee index
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
            ->route('dashboard.invoices')
            ->with('deleted', 'The invoice was deleted successfully!');
    }
}
