<?php

namespace App\Services;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;

class FirebaseService
{
    public static function connectdatabase()
    {
        $firebase = (new Factory)
            // ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            // ->withDatabaseUri(env("FIREBASE_DATABASE_URL"));
            ->withServiceAccount(__DIR__.'/concise-clock-347102-firebase-adminsdk-x4cnb-4f610c4f04.json')
            ->withDatabaseUri('https://concise-clock-347102-default-rtdb.firebaseio.com/');

        return $firebase->createDatabase();
        
    }

    public static function connectauth()
    {
        $firebase = (new Factory)
            // ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            // ->withDatabaseUri(env("FIREBASE_DATABASE_URL"));
            ->withServiceAccount(__DIR__.'/concise-clock-347102-firebase-adminsdk-x4cnb-4f610c4f04.json')
            ->withDatabaseUri('https://concise-clock-347102-default-rtdb.firebaseio.com/');

        return $firebase->createAuth();
    }

    public static function connect()
    {
        $firebase = (new Factory)
            // ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            // ->withDatabaseUri(env("FIREBASE_DATABASE_URL"));
            ->withServiceAccount(__DIR__.'/concise-clock-347102-firebase-adminsdk-x4cnb-4f610c4f04.json')
            ->withDatabaseUri('https://concise-clock-347102-default-rtdb.firebaseio.com/');

            return $firebase;
        // return $firebase->createFirestore();
    }

    // public static function database()
    // {
    //     $firebase = (new Factory)
    //         ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
    //         ->withDatabaseUri(env("FIREBASE_DATABASE_URL"));
    //     $firestore = $firebase->createFirestore();
    //     $database = $firestore->database();
    //     $testRef = $database->collection('TestUsers')->newDocument();
    //     return $testRef;
    // }
}