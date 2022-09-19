<?php

namespace App\Http\Controllers\API\v1\Auth;

use Exception;
use App\Models\User;
use App\Helpers\JSON;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken as FailedToVerifyTokenException;

class LoginController extends Controller
{
    /**
     * Return JSON response for this API endpoint
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function firebase(Request $request, FirebaseAuth $auth): JsonResponse
    {
        $validations = Validator::make($request->all(), [
            'token' => ['required', 'string', 'min:10', 'max:1000']
        ]);

        // Request validation failed
        if ($validations->fails())
            return JSON::error(
                'Missing or invalid data provided!',
                $validations->errors(),
                Response::HTTP_BAD_REQUEST
            );

        try {
            // Make sure the ID token is correct and is issued by our app
            $token = $auth->verifyIdToken($request->token);
            $uid = $token->claims()->get('sub'); // Get the user's `UID`

            // Find record
            $user = User::where('uid', $uid)->firstOrFail();

            $token = $user->getToken();
        } catch (FailedToVerifyTokenException | ModelNotFoundException $error) {
            return JSON::error(
                'Invalid or expired token provided!',
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } catch (Exception $error) {
            return JSON::error(
                'An unknown error occurred!',
                $error->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return JSON::success(data: compact('token'));
    }

    public function password(Request $request, FirebaseAuth $auth): JsonResponse
    {
        $validations = Validator::make($request->all(), [
            'email'    => ['required', 'string', 'min:6', 'max:100', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        // Request validation error
        if ($validations->fails())
            return JSON::error(
                'Invalid email or password!',
                null,
                Response::HTTP_UNAUTHORIZED
            );

        try {
            $user = User::where('email', $request->email)->firstOrFail();

            // Verify the password
            if (!Hash::check($request->password, $user->password))
                throw new InvalidArgumentException('Invalid email or password!');

            // Generate token for allowing client to login to Firebase
            $firebase_token = $auth->createCustomToken($user->uid)->toString();

            $token = $user->getToken(); // Generate new token
        } catch (ModelNotFoundException $error) { // Email does not exists
            return JSON::error(
                'Invalid email or password!',
                null,
                Response::HTTP_NOT_FOUND
            );
        } catch (InvalidArgumentException $error) { // Invalid password entered
            return JSON::error(
                'Invalid email or password!',
                null,
                Response::HTTP_UNAUTHORIZED
            );
        } catch (Exception $error) { // Unknown error
            return JSON::error(
                'An unknown error occurred!',
                $error->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return JSON::success(data: compact('token', 'firebase_token'));
    }
}
