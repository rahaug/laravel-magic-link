<?php namespace RolfHaug\TokenAuth\Tests;

use RolfHaug\TokenAuth\LoginToken;
use RolfHaug\TokenAuth\Tests\Helpers\User;

class LoginTokenTest extends TestCase
{
    /** @test */
    public function it_generates_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);

        $this->assertStringStartsWith($user->id . ":", $token);
    }

    /** @test */
    public function it_validates_generated_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);
        $this->assertTrue(LoginToken::validate($token));
    }
    
    /** @test */
    public function it_requires_colon_in_token()
    {
        $user = $this->createUser();
        $token = LoginToken::generate($user);
        $segments = explode(":", $token);

        $this->assertFalse(LoginToken::validate("mytoken"));
        $this->assertTrue(LoginToken::validate($user->id . ":" . $segments[1]));
    }

    /** @test */
    public function it_invalidates_token_if_first_segment_is_not_numeric()
    {
        $this->assertFalse(LoginToken::validate("notNumeric-token"));
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
    public function it_authorized_user_if_token_is_valid()
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

        $token = $fakeUser->id . ":" . explode(":", $token)[1];

        $this->assertFalse(LoginToken::validate($token));
        $this->assertFalse(LoginToken::authenticate($token));
    }
}