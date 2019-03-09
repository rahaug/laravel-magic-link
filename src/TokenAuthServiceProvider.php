<?php namespace RolfHaug\TokenAuth;

use Illuminate\Support\ServiceProvider;

class TokenAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/auth-token.php', 'auth');
    }
}
