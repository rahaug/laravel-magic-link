<?php namespace RolfHaug\TokenAuth\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Faker\Factory;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;
use RolfHaug\TokenAuth\Tests\Helpers\CreateMemoryDatabaseTrait;
use RolfHaug\TokenAuth\Tests\Helpers\User;

abstract class TestCase extends BaseTestCase
{
    use CreateMemoryDatabaseTrait;

    protected function setUp()
    {
        parent::setUp();
        $this->createMemoryDatabase();
        $this->migrateDatabase();

        $this->setConfig();
    }

    private function migrateDatabase()
    {
        DB::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * @param array $overrides
     * @param int $amount
     *
     * @return \RolfHaug\TokenAuth\Tests\User
     */
    function createUser($overrides = [], $amount = 1)
    {
        $users = new \Illuminate\Database\Eloquent\Collection;
        for ($i = 0; $i < $amount; $i++) {
            $user = User::create([
                'name' => Factory::create()->name,
                'email' => Factory::create()->email,
                'password' => bcrypt(Factory::create()->password)
            ], $overrides);
            $users->push($user);
        }
        return (count($users) > 1) ? $users : $users[0];
    }

    private function setConfig()
    {
        // Load config
        $auth = require __DIR__ . '/Helpers/config/auth.php';

        // Change user model
        $auth['providers']['users']['model'] = User::class;

        // Merge package config
        $config = require __DIR__ . '/../src/config/auth-token.php';

        $auth = array_merge($auth, $config);

        // Set config
        $this->app['config']->set('auth', $auth);


        // Required config for tests
        $this->app['config']->set('api.key', str_random(64));
    }
}