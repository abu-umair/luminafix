<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Helpers\FirebaseHelper;
use Illuminate\Support\Facades\Session;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class Users_LuminaController extends Controller
{
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connectdatabase();
        $this->auth = \App\Services\FirebaseService::connectauth();
    }
    /**
     * Show data table for users management
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return Illuminate\View\View
     */
    public function index(Request $request)
    {

        $user = $request->user();

        return view('dashboard.users', [
            'users' => User::where('id', '!=', $user->id)->get()
        ]);
    }

    /**
     * Validate incoming user data and create new record in database
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Kreait\Firebase\Contract\Auth  $auth
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'email' => [
                'required',
                'string',
                'min:6',
                'max:100',
                'email',
                Rule::unique('users', 'email')
            ],
            'role' => ['string', Rule::in(['admin', 'user'])],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
        ]);
        
        // If request has phone number then validate it and make sure the phone
        // number is not assigned assigned to another user
        
        if ($request->has('phone') && is_string($request->phone) && strlen($request->phone) > 0)
            $request->validate([
                'phone' => [
                    'string',
                    'min:9',
                    'max:15',
                    'regex:/\+(\d){9,15}/',
                    Rule::unique('users', 'phone')
                ]
            ]);
        dd($request);
        try {
            $newUser = $this->auth->createUserWithEmailAndPassword($email, $pass);
            dd($newUser);
        } catch (\Throwable $e) {
            switch ($e->getMessage()) {
                case 'The email address is already in use by another account.':
                    dd("Email sudah digunakan.");
                    break;
                case 'A password must be a string with at least 6 characters.':
                    dd("Kata sandi minimal 6 karakter.");
                    break;
                default:
                    dd($e->getMessage());
                    break;
            }
        }
           
        // try {
        //     // This will create a Firebase user for along with a local user
        //     FirebaseHelper::createUser(
        //         name: $request->name,
        //         email: $request->email,
        //         role: $request->role,
        //         phone: $request->phone,
        //         password: $request->password
        //     );
        // } catch (Exception $error) {
        //     return redirect()
        //         ->back()
        //         ->with('message', $error->getMessage());
        // }

        return redirect()
            ->route('dashboard.users')
            ->with('created', 'The user was created successfully!');
    }

    /**
     * Validate incoming user data and create new record in database
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\User  $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $updateData = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
        ]);

        // If request has email then validate it and make sure the email is not
        // assigned assigned to another user
        if ($request->has('email') && $request->email !== $user->email)
            $updateData['email'] = $request->validate([
                'email' => [
                    'string',
                    'min:6',
                    'max:255',
                    'email',
                    // The new email must not be linked with an existing
                    // account!
                    Rule::unique('users', 'email')
                ],
            ])['email'];

        // If request has phone number then validate it and make sure the phone
        // number is not assigned assigned to another user
        if (
            $request->has('phone') &&
            is_string($request->phone) && strlen($request->phone) > 0 &&
            $request->input('phone') !== $user->phone
        )
            $updateData['phone'] = $request->validate([
                'phone' => [
                    'string',
                    'min:9',
                    'max:15',
                    'regex:/\+(\d){9,15}/',
                    // Make sure the new phone number is not assigned to
                    // another user!
                    Rule::unique('users', 'phone')
                ]
            ])['phone'];

        // If a new password is passed then validate it, hash it and store the
        // hashed password
        if (
            $request->has('password') &&
            is_string($request->password) && strlen($request->password) > 0
        )
            $updateData['password'] = Hash::make(
                $request->validate([
                    'password' => [
                        'string',
                        Password::min(8)
                            ->mixedCase()
                            ->letters()
                            ->numbers()
                            ->symbols()
                    ]
                ])['password']
            );

        $user->update($updateData);

        if (config('firebase.enabled'))
            FirebaseHelper::syncUserData($user, $updateData['password'] ?? null);

        return redirect()
            ->route('dashboard.users')
            ->with('updated', 'The user was updated successfully!');
    }

    /**
     * Delete the requested user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            if (!$result = FirebaseHelper::deleteUser($user))
                throw new Exception('An error occurred while deleting user!');
        } catch (Exception $error) {
            return redirect()
                ->back()
                ->with('message', $error->getMessage());
        }

        return redirect()
            ->route('dashboard.users')
            ->with('deleted', 'The user was deleted successfully!');
    }

    public function signUp()
    {
        $email = "angelicdemon@gmail.com";
        $pass = "anya123";

        try {
            $newUser = $this->auth->createUserWithEmailAndPassword($email, $pass);
            dd($newUser);
        } catch (\Throwable $e) {
            switch ($e->getMessage()) {
                case 'The email address is already in use by another account.':
                    // dd("Email sudah digunakan.");
                    dd('cuma test');
                    break;
                case 'A password must be a string with at least 6 characters.':
                    dd("Kata sandi minimal 6 karakter.");
                    break;
                default:
                    dd($e->getMessage());
                    break;
            }
        }
    }
}
