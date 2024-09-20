<?php

namespace Danilowa\LaravelFactorySeederGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateFactoryAndSeederAllCommand extends Command
{
    protected $signature = 'make:a-all-factories-and-seeders';
    protected $description = 'Generate factories and seeders for all models that do not have them yet';

    public function handle()
    {
        $modelPath = app_path('Models');
        $models = $this->getModels($modelPath);

        if (empty($models)) {
            $this->info("No models found in {$modelPath}");
            return 0;
        }

        foreach ($models as $model) {
            if (!$this->factoryExists($model)) {
                $this->call('make:a-factory', ['model' => $model]);
            } else {
                $this->warn("Factory for model {$model} already exists.");
            }

            if (!$this->seederExists($model)) {
                $this->call('make:a-seeder', ['model' => $model]);
            } else {
                $this->warn("Seeder for model {$model} already exists.");
            }
        }

        $this->info('Factories and seeders generated successfully.');
        return 0;
    }

    protected function getModels($modelPath)
    {
        $models = [];
        if (!File::isDirectory($modelPath)) {
            return $models;
        }

        $files = File::files($modelPath);
        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $models[] = $filename;
        }

        return $models;
    }

    protected function factoryExists($model)
    {
        $factoryPath = base_path("database/factories/{$model}Factory.php");
        return File::exists($factoryPath);
    }

    protected function seederExists($model)
    {
        $seederPath = base_path("database/seeders/{$model}Seeder.php");
        return File::exists($seederPath);
    }
}
