<?php

namespace RolfHaug\TokenAuth\Tests;


class AuthTokenConfigTest extends TestCase
{
    /** @test */
    public function token_config_is_merged_with_auth_config()
    {
        $config = config('auth.token');
        $this->assertIsArray($config);

        $this->assertArrayHasKey('parameter', $config);
        $this->assertArrayHasKey('separator', $config);
        $this->assertArrayHasKey('routes', $config);
    }
}