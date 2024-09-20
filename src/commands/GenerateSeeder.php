<?php

namespace Danilowa\LaravelFactorySeederGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class GenerateSeeder extends Command
{
    protected $signature = 'make:a-seeder {model} {--count=10}';
    protected $description = 'Generate a smart seeder for the given model. by default --count=10';

    public function handle()
    {
        $customSeederCount = Config::get('factorySeederGenerator.custom_seeder_count', 10);
        $modelName = $this->argument('model');
        $modelClass = $this->getModelClass($modelName);
        $count = $this->option('count') ?? $customSeederCount;

        if (!$modelClass || !class_exists($modelClass)) {
            $this->error("Model class {$modelClass} does not exist.");
            return 1;
        }

        $seederPath = $this->generateSeeder($modelClass, $count);

        $this->info("Seeder created at {$seederPath}");
        return 0;
    }

    protected function getModelClass($modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            $this->error("Model class {$modelClass} does not exist.");
            return null;
        }

        if (!in_array('Illuminate\\Database\\Eloquent\\Factories\\HasFactory', class_uses($modelClass))) {
            $this->error("Model {$modelClass} does not use the HasFactory trait.");
            return null;
        }

        return $modelClass;
    }

    protected function generateSeeder($modelClass, $count)
    {
        $modelName = class_basename($modelClass);
        $seederName = "{$modelName}Seeder.php";
        $seederPath = base_path("database/seeders/{$seederName}");

        if (File::exists($seederPath)) {
            $this->info("Seeder already exists at {$seederPath}");
            return $seederPath;
        }

        $seederContent = $this->generateSeederContent($modelClass, $count);

        File::put($seederPath, $seederContent);
        return $seederPath;
    }

    protected function generateSeederContent($modelClass, $count)
    {
        $modelName = class_basename($modelClass);
        $seederNamespace = 'Database\\Seeders';

        $seederContent = "<?php\n\n";
        $seederContent .= "namespace {$seederNamespace};\n\n";
        $seederContent .= "use Illuminate\\Database\\Seeder;\n";
        $seederContent .= "use {$modelClass};\n\n";
        $seederContent .= "class {$modelName}Seeder extends Seeder\n";
        $seederContent .= "{\n";
        $seederContent .= "    public function run()\n";
        $seederContent .= "    {\n";
        $seederContent .= "        {$modelName}::factory()->count({$count})->create();\n";
        $seederContent .= "    }\n";
        $seederContent .= "}\n";

        return $seederContent;
    }
}
