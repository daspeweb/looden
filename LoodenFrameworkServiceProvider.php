<?php

namespace Looden\Framework;

use Illuminate\Support\ServiceProvider;
use Looden\Framework\Console\Commands\ModelReader;
use Looden\Framework\Console\Commands\TokenGenerator;

class LoodenFrameworkServiceProvider extends ServiceProvider {

    protected $commands = [
        ModelReader::class,
        TokenGenerator::class,
    ];
    public function boot()
    {
        $this->publishes([__DIR__.'/database/migrations/' => database_path('migrations')]);
        $this->publishes([__DIR__.'/config/looden.php' => config_path('looden.php')]);
        $this->loadRoutesFrom(__DIR__.'/routes/api-routes.php');
    }
    public function register()
    {
        $this->commands($this->commands);
    }
}