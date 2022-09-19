<?php

namespace App\Http\Middleware;

use App\Helpers\JSON;
use Illuminate\Http\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson())
            return JSON::error(
                'Only logged in users have access to this resource!',
                null,
                Response::HTTP_FORBIDDEN
            );

        return route('login');
    }
}
