<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use CStr;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connectdatabase();
        $this->auth = \App\Services\FirebaseService::connectauth();
        $this->connect = \App\Services\FirebaseService::connect();
    }


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
            'dob' => ['required', 'date_format:Y-m-d'],
            'phone' => ['string','min:9','max:15','regex:/\+(\d){9,15}/', 'unique:users,phone,'.Auth::user()->id],
        ]);

        $user = $request->user();

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

        // update firestore
        $dataFirestore = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];
        
        $connect = $this->connect->createFirestore();
        $newDatabase = $connect->database();
        $testRef = $newDatabase->collection('users')->document($user->uid);
        // $testRef->update($dataFirestore);
        $testRef->set($dataFirestore, [
            'merge' => true
        ]);
        
        // If a password is provided then change it in Firebase
        if ($request->password){
            $this->auth->changeUserPassword($user->uid, $request->password);
        } 

        $user->update($validated);

        // TODO: update di firebase, sebelumnya pada phone, date of birth masih error (solusinya pakau validasi seperti name saja)
        // dd('success');
        // FirebaseHelper::syncUserData($user, $validated['password'] ?? null);

        return redirect()
            ->route('dashboard.profile')
            ->with('updated', 'Your profile was updated!');
    }
}
