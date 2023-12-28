<?php

namespace Emmanuelabraham\Superban;

use Illuminate\Support\ServiceProvider;
use Emmanuelabraham\Superban\Drivers\RedisDriver;
use Emmanuelabraham\Superban\Drivers\DatabaseDriver;

class SuperbanServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('superban', function ($app) {
            $driver = $app['config']['superban.driver'];

            if ($driver === 'redis') {
                return new RedisDriver($app['redis']);
            }

            if ($driver === 'database') {
                return new DatabaseDriver($app['db']);
            }

            throw new \Exception("Unsupported driver: $driver");
        });
    }
}