<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Helpers\FirebaseHelper;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connectdatabase();
        $this->auth = \App\Services\FirebaseService::connectauth();
        $this->connect = \App\Services\FirebaseService::connect();
    }
    /**
     * Show data table for users management
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return Illuminate\View\View
     */
    public function index(Request $request): View
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
    public function store(Request $request): RedirectResponse
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
           
        try {
            $is_admin = $request->role == 'admin' ? 'true' : 'false';

            $newUser = $this->auth->createUserWithEmailAndPassword($request->email, $request->password);
            $connect = $this->connect->createFirestore();
            $newDatabase = $connect->database();
            $testRef = $newDatabase->collection('users')->document($newUser->uid);
            
            // create db
            $user = User::create([
                'uid'       => $newUser->uid ,
                'name'      => $request->name,
                'email'     => $request->email ,
                'phone'     => $request->phone ,
                // 'dob'       => $request->name ,
                'role'      => $request->role ,
                'password'  => Hash::make($request->password) ,
            ]);
            
            // create firestore
            $testRef->set([
                'user_id' => $user->id,
                'email' => $request->email,
                'is_admin' => $is_admin,
                'name' => $request->name,
                'phone' => $request->phone,
                'user_image' => null,
            ]);
            
            

        } catch (\Throwable $e) {
            switch ($e->getMessage()) {
                case 'The email address is already in use by another account.':
                    // dd("Email sudah digunakan.");
                    break;
                case 'A password must be a string with at least 6 characters.':
                    // dd("Kata sandi minimal 6 karakter.");
                    break;
                default:
                    dd($e->getMessage());
                    break;
            }
        }

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
        try {

            // update firestore
            $is_admin = $request->role == 'admin' ? 'true' : 'false';
            $dataFirestore = [
                'email' => $request->email,
                'is_admin' => $is_admin,
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

            // New email provided change it in Firebase
            if ($user->email != $request->email){
                $this->auth->changeUserEmail($user->uid, $request->email);
            }
            
            
            // If a password is provided then change it in Firebase
            if ($request->password){
                $this->auth->changeUserPassword($user->uid, $request->password);
            } 
            
            
            // update database
            $user->update($updateData);

            //update firestore
            // if (config('firebase.enabled'))
            //     FirebaseHelper::syncUserData($user, $updateData['password'] ?? null);
        
        } catch (\Throwable $e) {
            switch ($e->getMessage()) {
                case 'The email address is already in use by another account.':
                    // dd("Email sudah digunakan.");
                    break;
                case 'A password must be a string with at least 6 characters.':
                    // dd("Kata sandi minimal 6 karakter.");
                    break;
                default:
                    dd($e->getMessage());
                    break;
            }
        }
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
            // delete auth user
            $this->auth->deleteUser($user->uid);

            // delete firestore user
            $connect = $this->connect->createFirestore();
            $newDatabase = $connect->database();
            $testRef = $newDatabase->collection('users')->document($user->uid)->delete();

            // delete database
            $user->delete();

            // if (!$result = FirebaseHelper::deleteUser($user))
            //     throw new Exception('An error occurred while deleting user!');
        } catch (Exception $error) {
            return redirect()
                ->back()
                ->with('message', $error->getMessage());
        }

        return redirect()
            ->route('dashboard.users')
            ->with('deleted', 'The user was deleted successfully!');
    }
}
