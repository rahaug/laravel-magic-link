<?php

namespace RolfHaug\TokenAuth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use RolfHaug\TokenAuth\LoginToken;

class TokenAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // @TODO: extract invalid routes to config file and check against a list of routes
        if($request->token && ! $request->is('password/reset*'))
        {
            // Make sure to validate LoginToken before we take action (?token= might be used by others too)
            if (LoginToken::validate($request->token) && !LoginToken::authenticate($request->token)) {
                return redirect('login')->with('invalid_token', true);
            }
        }

        return $next($request);
    }
}
