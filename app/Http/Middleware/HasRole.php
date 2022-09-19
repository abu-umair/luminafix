<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if ($request->user()->role !== $role)
            return response()
                ->view('error', [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Unauthorized'
                ])
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);

        return $next($request);
    }
}
