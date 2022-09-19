<?php

namespace App\Helpers;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Auth\UserRecord as FirebaseUser;
use Kreait\Firebase\Exception\Auth\UserNotFound as UserNotFoundException;

class FirebaseHelper
{
    /**
     * Creates a new user record in Firebase Auth and local database and also
     * adds user details to Cloud Firestore under appropriate document path.
     *
     * @param string  $name
     * @param string  $email
     * @param ?string  $phone
     * @param ?string  $password
     * @param ?string  $role
     */
    public static function createUser(
        string $name,
        string $email,
        ?string $phone = null,
        ?string $password = null,
        ?string $role = null
    ): User {
        $password = $password ?: config('lumina.accounts.defaults.password');
        $user = null;

        DB::transaction(function () use (&$user, $name, $email, $phone, $role, $password) {
            $record = static::createFirebaseUser(
                name: $name,
                email: $email,
                uid: null, // Creating a new user
                phone: $phone,
                password: $password,
            );
        
            

            // Got an unexpected result
            // if (!$record instanceof FirebaseUser)
            //     return false;

            // Create new user in local database
            $user = User::create([
                'uid' => 'bb',
                // 'uid' => $record->uid,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
                'password' => Hash::make($password)
            ]);
            

            // Update user record in Cloud Firestore
            static::updateUserFirestore($user);
        });

        if (!$user)
            throw new Exception('An unknown error occurred while creating user!');

        return $user;
    }

    /**
     * Deletes provided user's record in both Firebase auth and local database,
     * returns `true` if successful otherwise `false`.
     *
     * @param \App\Models\User  $user
     * @param ?bool  $now
     *
     * @return bool
     */
    public static function deleteUser(User $user, ?bool $now = false): bool
    {
        if (config('firebase.enabled')) {
            // Delete Firebase user and their Firestore record
            if (!static::deleteFirebaseUser($user))
                return false;

            // Deleting Firestore right away
            if ($now && !static::deleteUserFirestore($user))
                return false;
        }

        // Delete user locally
        $user->delete();

        return true;
    }

    /**
     * Updates the user record in Firebase with local user record and
     * synchronizes both data sources.
     *
     * @param \App\Models\User  $user
     * @param ?string  $password
     *
     * @return bool|string
     */
    public static function syncUserData(
        User $user,
        ?string $password = null,
        ?bool $createNew = true
    ): bool|string {
        if (!config('firebase.enabled')) return true;

        // The user does not exists in Firebase create a new one
        if (!$record = static::getFirebaseUser($user)) {
            if (!$createNew)
                throw new Exception('Provided user\'s does not exist in Firebase!');

            $record = static::createFirebaseUser(
                name: $user->name,
                email: $user->email,
                uid: $user->uid,
                phone: $user->phone,
                password: $password
            );
            static::updateUserFirestore($user);
        }

        $auth = Firebase::auth();

        // New email provided change it in Firebase
        if ($user->email !== $record->email)
            $auth->changeUserEmail($user->uid, $user->email);

        // If a password is provided then change it in Firebase
        if ($password) $auth->changeUserPassword($user->uid, $password);

        // Update data in Firebase
        $auth->updateUser($user->uid, [
            'displayName' => $user->name,
            'phoneNumber' => $user->phone,
        ]);

        // Update data in Cloud Firestore
        static::updateUserFirestore($user);

        return true;
    }

    /**
     * Fetches the Firebase `UserRecord` of provided user or `false` if it does
     * not exists in Firebase also throws an exception if `mustExists` is
     * passed true.
     *
     * @param \App\Models\User  $user
     * @param bool  $mustExist
     *
     * @return \Kreait\Firebase\Auth\UserRecord|bool
     */
    private static function getFirebaseUser(
        User $user,
        bool $mustExist = false
    ): FirebaseUser | bool {
        if (!config('firebase.enabled')) return true;

        $auth = Firebase::auth();

        try {
            $record = $auth->getUser($user->uid);

            // If user exists then return the Firebase `UserRecord`
            if ($record?->uid)
                return $record;
        } catch (UserNotFoundException $error) {
            // Log event for easier development
            Log::debug('User not found in Firebase but exist locally!', [
                'id' => $user->id,
                'uid' => $user->uid,
                'error' => $error->getMessage()
            ]);

            // If record must exist
            if ($mustExist)
                throw new Exception(
                    'Could not sync user data as record for this user does not exist in Firebase!'
                );
        }

        return false;
    }

    /**
     * Creates a new user record in Firebase against provided user details
     *
     * @param string  $name
     * @param string  $email
     * @param ?string  $uid
     * @param ?string  $phone
     * @param ?string  $password
     *
     * @return \Kreait\Firebase\Auth\UserRecord|string|bool
     */
    private static function createFirebaseUser(
        string $name,
        string $email,
        ?string $uid = null,
        ?string $phone = null,
        ?string $password = null
    ): FirebaseUser|string|bool {
        if (!config('firebase.enabled')) return true;

        $auth = Firebase::auth();

        $data = [
            'displayName'   => $name,
            'email'         => $email,
            'emailVerified' => true,
            'password'      => $password ?: config('lumina.accounts.defaults.password'),
            'disabled'      => false,
        ];

        // Add fields if data is passed as passing null fields raise an
        // invalid arguments exception
        if ($phone) $data['phone'] = $phone;
        if ($uid) $data['uid'] = $uid;

        try {
            $user = $auth->createUser($data);

            // Necessary data is not present on user modal
            if (!$user?->uid) return false;

            return $user;
        } catch (Exception $error) {
            return $error->getMessage();
        }

        return false;
    }

    /**
     * Deletes the user in Firebase Auth against provided user's `uid`, returns
     * `true` if successful else `false`.
     *
     * @param \App\Models\User  $user
     *
     * @return bool
     */
    private static function deleteFirebaseUser(User $user): bool
    {
        if (!config('firebase.enabled')) return true;

        $auth = Firebase::auth();

        try {
            $auth->deleteUser($user->uid);
        } catch (Exception $error) {
            Log::error('An error occurred while delete user record form Firebase!', [
                'id' => $user->id,
                'uid' => $user->uid,
                'error' => $error->getMessage()
            ]);

            return false;
        }

        return true;
    }

    /**
     * Creates/updates the appropriate document for passed user with user data,
     * returns `true` if successful else `false`.
     *
     * @param \App\Models\User  $user
     *
     * @return array|bool
     */
    private static function updateUserFirestore(User $user): array|bool
    {
        if (!config('firebase.enabled')) return true;

        $firestore = Firebase::firestore()->database();

        $data = [
            'name'     => $user->name,
            'email'    => $user->email,
            'is_admin' => $user->isAdmin,
        ];

        // Empty values throw error
        if ($user->phone) $data['phone'] = $user->phone;
        if ($user->dob) $data['phone'] = $user->unixDateOfBirth;

        try {
            return $firestore
                ->collection('users')
                ->document($user->uid)
                ->set($data, ['merge' => true]);
        } catch (Exception $error) {
            Log::error('An error occurred while updating user\'s Firestore record!', [
                'id'    => $user->id,
                'uid'   => $user->uid,
                'error' => $error->getMessage()
            ]);
        }

        return false;
    }

    /**
     * Deletes user record in Firestore of provided user through its `uid`,
     * returns `true` if successful else `false`.
     *
     * @param \App\Models\User  $user
     *
     * @return bool
     */
    private static function deleteUserFirestore(User $user): bool
    {
        if (!config('firebase.enabled')) return true;

        $firestore = Firebase::firestore()->database();

        try {
            $firestore
                ->collection('users')
                ->document($user->uid)
                ->delete();
        } catch (Exception $error) {
            Log::error('An error occurred while deleting user\'s Firestore record!', [
                'id'    => $user->id,
                'uid'   => $user->uid,
                'error' => $error->getMessage()
            ]);

            return false;
        }

        return true;
    }
}
