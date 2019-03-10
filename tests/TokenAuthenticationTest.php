<?php

namespace RolfHaug\TokenAuth\Tests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RolfHaug\TokenAuth\Middleware\TokenAuthentication;
use RolfHaug\TokenAuth\LoginToken;

class TokenAuthenticationTest extends TestCase
{
    private $parameter;
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->parameter = config('auth.token.parameter');
    }

    private function newRequest($user = null)
    {
        $this->user = $user ?: $this->createUser();
        $request = new Request;
        $request->replace([$this->parameter => LoginToken::generate($this->user)]);

        return $request;
    }

    /** @test */
    public function it_authenticates_user_with_valid_token()
    {
        $request = $this->newRequest();

        $middleware = new TokenAuthentication;
        $middleware->handle($request, function(){});

        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function it_does_not_authenticated_user_with_invalid_token()
    {
        $user = $this->createUser();

        $request = new Request;
        $request->replace([
            $this->parameter => LoginToken::generate($user) . 'invalid'
        ]);

        $middleware = new TokenAuthentication;
        $response = $middleware->handle($request, function() {});

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertGuest();
    }

    /** @test */
    public function it_does_not_redirect_if_no_token_is_provided()
    {
        $middleware = new TokenAuthentication;
        $response = $middleware->handle(new Request, function(){
            // This proves that $next closure/middleware was called
            return false;
        });

        $this->assertFalse($response);
        $this->assertNotInstanceOf(RedirectResponse::class, $response);
    }

    /** @test */
    public function it_ignores_requests_from_configured_routes()
    {
        $request = $this->newRequest();

        // Route specified in default auth-token config
        $request->server->set('REQUEST_URI', '/password/reset');

        $middleware = new TokenAuthentication;
        $response = $middleware->handle($request, function(){
            // This proves that $next closure/middleware was called
            return false;
        });

        $this->assertFalse($response);
        $this->assertGuest();
    }
}