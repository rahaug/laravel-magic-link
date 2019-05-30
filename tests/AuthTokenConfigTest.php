<?php

namespace RolfHaug\TokenAuth\Tests;

class AuthTokenConfigTest extends TestCase
{
    /** @test */
    public function token_config_is_merged_with_auth_config()
    {
        $config = config('auth');
        $this->assertIsArray($config);

        $this->assertArrayHasKey('token-parameter', $config);
        $this->assertArrayHasKey('token-separator', $config);
        $this->assertArrayHasKey('token-exclude-routes', $config);
    }
}
