# Laravel Magic Link

This Laravel package allows you to create magic login links to let users log in to any route with a URL (without password)

```
example.com/mypage/update-card?token=1234:4cd3cf4b1c56c3e6e8ebe22db4b82869
```
The [token hash](https://github.com/rahaug/laravel-magic-link/blob/master/src/LoginToken.php#L16) is generated using the unique `APP_KEY` of your Laravel project as salt. The token can be appended to any route.

The package is lightweight and does not perform unnecessary checks or database calls. 

## Installation
Installing the package is easy and requires three steps.

Require the package

```
composer require rolfhaug/laravel-magic-link
```

Register the middleware under the **web middleware group**:
```
// App/Http/Kernel.php

protected $middlewareGroups = [
    'web' => [
        //...
        \RolfHaug\TokenAuth\Middleware\TokenAuthentication::class,
    ]
];
```

Give the middleware a higher [priority](https://laravel.com/docs/5.8/middleware#sorting-middleware) than `\App\Http\Middleware\Authenticate::class`. 
> **Please note** It **must** have a higher priority to work with protected routes.

```
// App/Http/Kernel.php

protected $middlewarePriority = [
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    
    \RolfHaug\TokenAuth\Middleware\TokenAuthentication::class,
    
    \App\Http\Middleware\Authenticate::class,
    //...
];
``` 
> **Please note** If you don't have the `$middlewarePriority` property in your `Kernel.php`, you can grab it from `Illuminate\Foundation\Http\Kernel.php` which your kernel extends.

## Usage

As a user of this package you would normally generate tokens and append them to URL's.

### Generate Token

The `generate` method takes your user model as the first argument and return a unique and secure token.

```
$token = RolfHaug\TokenAuth\LoginToken::generate($user);
// 1234:4cd3cf4b1c56c3e6e8ebe22db4b82869
```


You can pass a boolean flag (defaults to false) as a second argument if you want to receive the token parameter as well

```
$token = RolfHaug\TokenAuth\LoginToken::generate($user, true);
// token=1234:4cd3cf4b1c56c3e6e8ebe22db4b82869

```

### Routes and URL's
There is many ways to create the URL's you need, here is a few common ways.

**Named Routes**

The `generateRoute` method will generate the URL to a named route and append a login token.
```
$url = RolfHaug\TokenAuth\LoginToken::generateRoute($user, 'web.mypage.billing');
```
You can pass additional arguments, like you would with the [route helper](https://laravel.com/docs/5.8/helpers#method-route).
```
$url = LoginToken::generateRoute($user, 'user.show', [$user]);
```

**Free hand URL**

Use the optional `$withParameter` flag, as a second parameter, to get the configured query parameter for the tokens to easily append it to any URL.
```
$url = "example.com/mypage?" . RolfHaug\TokenAuth\LoginToken::generate($user, true);
```

**URL built with [http_build_query](http://php.net/http_build_query)**

Merge a token with other query parameters using the native `http_build_query` function.
```
$queryParameters = [
    'section' => 'billing'
];

$token = RolfHaug\TokenAuth\LoginToken::generateArray($user)

$url = "example.com/mypage?" . http_build_query(array_merge($queryParameters, $token));
```


## Configuration


The package can be customized in the `config/auth.php` file, by adding or overwriting the following options.
```
return [
    // Token Auth config (rolfhaug/laravel-magic-link package)
    
    'token-parameter' => 'token', // Parameter the middleware will look for in the request
    'token-separator' => ':', // Separator between user id and token hash
 
    // Disable middleware on following routes
    'token-exclude-routes' => [
        'password/reset*'
    ]
];
```

**User Model**

The token generator require the model that is defined in the `config/auth.php` file under "providers.users.model". By default, this is the `App\User` model.


## Use Cases

The package is designed to reduce friction for users. I've successfully increased valuable conversion rates with this package. 
Here is some ideas on when the package can be valuable.

**When a user is encouraged to take action in an email or SMS**
- When you send emails about abandoned shopping carts
- When a user must update their card to continue the service
- When your content is protected behind a login wall

**When prototyping projects or creating MVP's**

Sometimes it does not make sense to have an admin-tool for all the actions a user can take. Instead you can have a "log in as user" link in the admin dashboard, and use the user dashboard to do the necessary changes.

> **Protip** Log in as user in incognito tab to not loose your admin session :)

**Example**

As an admin, you might not need to be able to manage a user's address list in your ecommerce dashboard. Instead you can log in as the user, if you need to provide that support once in a while.
