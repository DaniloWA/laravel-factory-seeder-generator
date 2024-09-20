<?php

namespace Danilowa\LaravelFactorySeederGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Danilowa\LaravelFactorySeederGenerator\Commands\GenerateSeeder;
use Danilowa\LaravelFactorySeederGenerator\Commands\GenerateFactory;
use Danilowa\LaravelFactorySeederGenerator\Commands\GenerateFactoryAndSeederCommand;
use Danilowa\LaravelFactorySeederGenerator\Commands\GenerateFactoryAndSeederAllCommand;

class FactorySeederGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'factorySeederGenerator');


        $this->commands([
            GenerateFactory::class,
            GenerateSeeder::class,
            GenerateFactoryAndSeederCommand::class,
            GenerateFactoryAndSeederAllCommand::class,
        ]);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('factorySeederGenerator.php'),
            ], 'config');
        }
    }
}
