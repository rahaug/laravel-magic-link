<?php
namespace RolfHaug\TokenAuth;

use Illuminate\Support\Facades\Auth;
use RolfHaug\TokenAuth\Exceptions\InvalidAuthModel;

class LoginToken
{
    public static function generate($user, $withParameter = false)
    {
        // Resolve Auth/User Model from config
        $authModel = resolve(config('auth.providers.users.model'));

        if( ! $user instanceof $authModel) throw new InvalidAuthModel;

        $token = $user->id . config('auth.token.separator') . md5($user->id . $user->password . $user->email . config('app.key') . $user->id);

        return $withParameter ? config('auth.token.parameter') . '=' . $token : $token;
    }

    public static function generateArray($user)
    {
        list($key, $value) = explode('=', self::generate($user, true));
        
        return [$key => $value];
    }

    public static function isTokenFormatValid($token)
    {
        $segments = explode(config('auth.token.separator'), $token);

        // Token must include the separator and first part must be numeric
        return is_array($segments) && is_numeric($segments[0]);
    }

    public static function validate($token)
    {
        // Token must include the separator and first part must be numeric
        if( ! self::isTokenFormatValid($token)) return false;

        $user = self::user($token);

        return $user ? $token === self::generate($user) : false;
    }

    public static function authenticate($token)
    {
        if(self::validate($token)) {
            Auth::login(self::user($token));
            return true;
        }

        return false;
    }

    public static function user($token)
    {
        $segments = explode(config('auth.token.separator'), $token);
        $user = resolve(config('auth.providers.users.model'));
        return $user::find($segments[0]);
    }
}