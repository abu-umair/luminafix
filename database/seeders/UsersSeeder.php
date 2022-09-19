<?php

namespace Database\Seeders;

use App\Models\User;
use App\Helpers\FirebaseHelper;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Auth\UserRecord as FirebaseUser;
use Kreait\Firebase\Contract\Firestore;

class UsersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(FirebaseAuth $auth, Firestore $firestore)
    {
        // Remove all users and their data from Firebase
        if (config('firebase.enabled')) {
            // Grab all registered users
            $uids = collect($auth->listUsers())
                ->map(fn (FirebaseUser $user) => $user->uid)
                ->toArray();

            // Delete all users from Firebase
            if (count($uids) > 0)
                $auth->deleteUsers($uids, true);

            $database = $firestore->database();

            // Delete all documents form Firestore
            collect($uids)->each(function (string $uid) use ($database) {
                try {
                    $database
                        ->collection('users')
                        ->document($uid)
                        ->delete();
                } catch (Exception $error) {
                    // The document must be already be deleted
                }
            });
        }

        // Delete all users from local database
        User::all()->each(fn (User $user) => $user->delete());

        if (config('firebase.enabled'))
            // First save user in Firebase then in local database
            FirebaseHelper::createUser(
                name: 'Admin',
                email: 'admin@lumina.org',
                role: 'admin'
            );
        else
            // Store user in database only
            User::factory()
                ->state([
                    'name' => 'Admin',
                    'email' => 'admin@lumina.org',
                    'role' => 'admin'
                ])
                ->create();


        // Generate fake data for normal users
        $users = User::factory()
            ->count(15)
            ->state(['role' => 'user']);

        if (config('firebase.enabled'))
            // Create records in Firebase and local database
            $users->make()
                ->map(function (User $user) {
                    FirebaseHelper::createUser(
                        name: $user->name,
                        email: $user->email,
                        phone: $user->phone,
                        role: $user->role
                    );
                });
        else
            $users->create(); // Only create records in database
    }
}
