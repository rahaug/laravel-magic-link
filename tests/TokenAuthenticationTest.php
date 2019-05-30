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

    public function setUp() : void
    {
        parent::setUp();
        $this->parameter = config('auth.token-parameter');
    }

    private function newRequest($user = null)
    {
        $this->user = $user ?: $this->createUser();
        $request = new Request;
        $request->replace([$this->parameter => LoginToken::generate($this->user)]);

        return $request;
    }

    private function assertMiddlewareToNotAuth($request)
    {
        $middleware = new TokenAuthentication;
        $response = $middleware->handle($request, function(){
            // This proves that $next closure/middleware was called
            return false;
        });

        $this->assertFalse($response);
        $this->assertGuest();
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
    public function it_redirect_user_to_login_page_if_valid_token_format_and_invalid_token_hash_is_given()
    {
        $user = $this->createUser();

        $request = new Request;
        $request->replace([
            // Alter the hash of a real token
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
        $this->assertMiddlewareToNotAuth(new Request);
    }

    /** @test */
    public function it_ignores_requests_from_configured_routes()
    {
        $request = $this->newRequest();

        // Route specified in default auth-token config
        $request->server->set('REQUEST_URI', '/password/reset');

        $this->assertMiddlewareToNotAuth($request);
    }

    /** @test */
    public function it_ignores_tokens_with_invalid_format()
    {
        $request = new Request;
        $token = 'invalid:invalid-format';
        $this->assertFalse(LoginToken::isTokenFormatValid($token));
        $request->replace([$this->parameter => $token]);

        $this->assertMiddlewareToNotAuth($request);
    }
}