<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VariablesController extends Controller
{
    /**
     * Show the view for editing variables
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $defaults = config('lumina.variables.default');
        $setting = Setting::firstOrCreate(
            ['key' => 'variables'],
            // If not created yet then create using default values
            ['value' => $defaults]
        );

        return view('dashboard.variables', [
            'safe'         => $setting->value['safe'],
            'span'         => $setting->value['span'],
            'fee_theta'    => $setting->value['fee_theta'],
            'last_updated' => $setting->lastUpdated
        ]);
    }

    /**
     * Validate variable data and update it in database
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $variables = $request->validate([
            'span'      => ['required', 'numeric', 'min:0', 'max:100'],
            'safe'      => ['required', 'numeric', 'min:0', 'max:100'],
            'fee_theta' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Setting::where('key', 'variables')
            ->update(['value' => $variables]);

        return redirect()
            ->route('dashboard.variables')
            ->with('updated', 'The variables were updated successfully!');
    }
}
