<?php

namespace Danilowa\LaravelFactorySeederGenerator\Commands;

use Illuminate\Console\Command;

class GenerateFactoryAndSeederCommand extends Command
{
    protected $signature = 'make:a-factory-and-seeder {model} {--count=10}';
    protected $description = 'Generate a smart factory and seeder (by default --count=10) for the given model';

    public function handle()
    {
        $model = $this->argument('model');
        $count = $this->option('count');

        $this->call('make:a-factory', ['model' => $model]);

        $this->call('make:a-seeder', ['model' => $model, '--count' => $count]);

        $this->info("Factory and seeder created for {$model} with count of {$count} records");
    }
}
