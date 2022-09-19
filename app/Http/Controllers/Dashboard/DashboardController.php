<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\Invoice;
use App\Models\Project;
use Exception;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $projects = Project::where('status', '!=', 'finished')
            ->with(['invoices'])
            ->get();

        // Number of active projects
        $active = $projects->count();

        // Total number of projects
        $total = Project::count();

        // Total progress of all projects
        if ($projects->count() === 0)
            $progress = 0;
        else
            $progress = round(($projects
                ->reduce(fn ($carry, $project) => $carry + $project->workload, 0) * 100
            ) / ($projects->count() * 100));

        $total_progress = $projects->reduce(fn ($carry, $project) => $carry + $project->workload, 0);

        // Budget of active projects
        $contracts = (int) $projects
            ->reduce(function (int $carry, Project $project) {
                return $carry + $project->budget;
            }, 0);

        // Sum of paid invoices of active projects
        $paid_invoices = (int) $projects
            ->reduce(function (int $carry, Project $project) {
                $invoice = (int) $project->invoices->reduce(function ($carry, Invoice $invoice) {
                    return $carry + $invoice->amount;
                });

                return $carry + $invoice;
            }, 0);

        $remaining_fee = $contracts - $paid_invoices;

        $calculations = null;

        try {
            $calculations = Cost::currentMonth()->calculate();
        } catch (Exception $error) {
        }

        return view(
            'dashboard.index',
            compact('active', 'total', 'contracts', 'remaining_fee', 'calculations', 'progress')
        );
    }
}
