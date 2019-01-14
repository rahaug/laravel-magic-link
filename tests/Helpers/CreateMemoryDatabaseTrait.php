<?php

namespace RolfHaug\TokenAuth\Tests\Helpers;

use Illuminate\Database\Capsule\Manager as DB;

trait CreateMemoryDatabaseTrait
{
    protected function createMemoryDatabase()
    {
        $database = new DB;
        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->bootEloquent();
        $database->setAsGlobal();
    }
}