<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use CStr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile details page.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('dashboard.profile', [
            'user' => $request->user()
        ]);
    }

    /**
     * Validate profile data and update it in database.
     *
     * @param \Illuminate\Http\Reques
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:30'],
        ]);

        $user = $request->user();

        // If date of birth is passed then validate it
        if ($request->has('dob') && is_string($request->dob) && strlen($request->dob) > 0)
            array_merge($request->validate([
                'dob' => ['required', 'date_format:Y-m-d']
            ]), $validated);

        // If a new phone number is passed then validate it
        if (
            $request->has('phone') &&
            is_string($request->phone) && strlen($request->phone) > 0 &&
            $request->input('phone') !== $user->phone
        )
            array_merge($request->validate([
                'phone' => [
                    'string',
                    'min:9',
                    'max:15',
                    'regex:/\+(\d){9,15}/',
                    // Make sure the new phone number is not assigned to
                    // another user!
                    Rule::unique('users', 'phone')
                ]
            ]), $validated);

        // If a new password is passed then validate it and hash it
        if (
            $request->has('password') &&
            is_string($request->password) && strlen($request->password) > 0
        )
            $validated['password'] = Hash::make(
                $request->validate([
                    'password' => [
                        'string',
                        'regex:/[a-z]/',
                        'regex:/[A-Z]/',
                        'regex:/[0-9]/',
                        'regex:/[@$!%*#?&]/'
                    ]
                ])['password']
                    // ]),$validated
            );

        $user->update($validated);

        // TODO: update di firebase, sebelumnya pada phone, date of birth masih error (solusinya pakau validasi seperti name saja)
        dd('success');
        FirebaseHelper::syncUserData($user, $validated['password'] ?? null);

        return redirect()
            ->route('dashboard.profile')
            ->with('updated', 'Your profile was updated!');
    }
}
