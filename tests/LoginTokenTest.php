<?php namespace RolfHaug\TokenAuth\Tests;

use Illuminate\Support\Facades\Route;
use RolfHaug\TokenAuth\LoginToken;
use RolfHaug\TokenAuth\Tests\Helpers\User;

class LoginTokenTest extends TestCase
{
    private $separator;
    private $parameter;

    protected function setUp() : void
    {
        parent::setUp();

        // Set config to make sure tests pass
        $this->parameter = 'token';
        $this->separator = ':';
        config()->set('auth.token-parameter', $this->parameter);
        config()->set('auth.token-separator', $this->separator);
    }

    /** @test */
    public function it_generates_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);

        $this->assertStringStartsWith($user->id . $this->separator, $token);
    }

    /** @test */
    public function it_generates_token_with_parameter()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user, true);
        $this->assertStringStartsWith($this->parameter . '=' . $user->id . $this->separator, $token);
    }

    /** @test */
    public function it_generates_token_with_named_route()
    {
        // Create a fake route
        Route::get('test-route', ['as' => 'mypage.billing']);

        $user = $this->createUser();
        $url = LoginToken::generateRoute($user, 'mypage.billing');

        // Construct expected output
        $expected =  route('mypage.billing') . '?' . LoginToken::generate($user, true);

        $this->assertEquals($expected, urldecode($url));
    }

    /** @test */
    public function it_generates_token_with_named_route_and_parameters()
    {
        Route::get('/user/{id}', ['as' => 'user.show']);
        $user = $this->createUser();

        $url = LoginToken::generateRoute($user, 'user.show', [$user]);

        $expected =  route('user.show', [$user]) . '?' . LoginToken::generate($user, true);

        $this->assertEquals($expected, urldecode($url));
    }

    /** @test */
    public function it_generates_token_in_array_format()
    {
        $user = $this->createUser();
        $token = LoginToken::generateArray($user);

        $this->assertIsArray($token);
        $this->assertEquals($this->parameter, key($token));
        $this->assertEquals(LoginToken::generate($user), $token[$this->parameter]);
    }

    /** @test */
    public function it_validates_generated_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);
        $this->assertTrue(LoginToken::validate($token));
    }
    
    /** @test */
    public function it_requires_separator_in_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);
        $segments = explode($this->separator, $token);

        $this->assertFalse(LoginToken::validate("invalid-token"));
        $this->assertTrue(LoginToken::validate($user->id . $this->separator . $segments[1]));
    }

    /** @test */
    public function it_invalidates_token_if_first_segment_is_not_numeric()
    {
        $this->assertFalse(LoginToken::validate("notNumeric" . $this->separator . "token"));
    }

    /** @test */
    public function it_returns_user_from_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);

        $this->assertInstanceOf(User::class, LoginToken::user($token));
        $this->assertEquals($user->id, (LoginToken::user($token))->id);
    }

    /** @test */
    public function it_authorized_user_with_valid_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);

        $this->assertTrue(LoginToken::authenticate($token));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_recognized_invalid_tokens()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user) . "manipulatedToken";

        $this->assertFalse(LoginToken::validate($token));
        $this->assertFalse(LoginToken::authenticate($token));
    }

    /** @test */
    public function it_recognize_that_user_is_changed()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);
        $fakeUser = $this->createUser();

        $token = $fakeUser->id . $this->separator . explode($this->separator, $token)[1];

        $this->assertFalse(LoginToken::validate($token));
        $this->assertFalse(LoginToken::authenticate($token));
    }
}