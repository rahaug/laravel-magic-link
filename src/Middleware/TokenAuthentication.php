<?php

namespace RolfHaug\TokenAuth\Middleware;

use Closure;
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
        $token = config('auth.token-parameter');

        if($request->has($token))
        {
            // Exclude auto login on certain routes (e.g. reset password)
            foreach(config('auth.token-exclude-routes') as $route)
            {
                if($request->is($route)) {
                    return $next($request);
                }
            }
            
            // Only respond to valid tokens (?token=.. might be used by others too)
            if ( LoginToken::isTokenFormatValid($request->$token) && ! LoginToken::authenticate($request->$token)) {
                return redirect('login')->with('invalid_token', true);
            }

            // User is authenticated at this point
        }

        return $next($request);
    }
}
