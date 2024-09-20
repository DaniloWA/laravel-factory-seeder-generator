<?php

namespace Danilowa\LaravelFactorySeederGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Illuminate\Database\Eloquent\Relations\Relation;

class GenerateFactory extends Command
{
    protected $signature = 'make:a-factory {model}';
    protected $description = 'Generate a smart factory for the given model';

    public function handle()
    {
        $modelName = $this->argument('model');
        $modelClass = $this->getModelClass($modelName);

        if (!$modelClass || !class_exists($modelClass)) {
            $this->error("Model class {$modelClass} does not exist.");
            return 1;
        }

        $factoryPath = $this->generateFactory($modelClass);

        $this->info("Factory created at {$factoryPath}");
        return 0;
    }

    protected function getModelClass($modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";
        return class_exists($modelClass) ? $modelClass : null;
    }

    protected function generateFactory($modelClass)
    {
        $modelName = class_basename($modelClass);
        $factoryName = "{$modelName}Factory.php";
        $factoryPath = base_path("database/factories/{$factoryName}");

        if (File::exists($factoryPath)) {
            $this->info("Factory already exists at {$factoryPath}");
            return $factoryPath;
        }

        $factoryContent = $this->generateFactoryContent($modelClass);

        File::put($factoryPath, $factoryContent);
        return $factoryPath;
    }

    protected function generateFactoryContent($modelClass)
    {
        $modelName = class_basename($modelClass);
        $factoryNamespace = 'Database\\Factories';

        $relatedModels = $this->getRelatedModels($modelClass);
        $imports = '';
        foreach ($relatedModels as $relatedModel) {
            $imports .= "use App\\Models\\{$relatedModel};\n";
        }

        $factoryContent = "<?php\n\n";
        $factoryContent .= "namespace {$factoryNamespace};\n\n";
        $factoryContent .= "use Illuminate\Database\Eloquent\Factories\Factory;\n\n";
        $factoryContent .= "{$imports}";
        $factoryContent .= "use {$modelClass};\n\n";
        $factoryContent .= "class {$modelName}Factory extends Factory\n";
        $factoryContent .= "{\n";
        $factoryContent .= "    protected \$model = {$modelName}::class;\n\n";
        $factoryContent .= "    public function definition()\n";
        $factoryContent .= "    {\n";
        $factoryContent .= "        return [\n";

        $fillableAttributes = $this->getModelFillableAttributes($modelClass);
        $relationships = $this->getModelRelationships($modelClass);
        $casts = $this->getModelCasts($modelClass);

        foreach ($fillableAttributes as $attribute) {
            if (array_key_exists($attribute, $relationships)) {
                $relatedModel = $relationships[$attribute];
                $factoryContent .= "            '{$attribute}' => {$relatedModel}::factory(),\n";
            } else {
                $factoryContent .= "            '{$attribute}' => " . $this->getFakerValueBasedOnCast($casts[$attribute] ?? null) . ",\n";
            }
        }

        $factoryContent .= "        ];\n";
        $factoryContent .= "    }\n";
        $factoryContent .= "}\n";

        return $factoryContent;
    }

    protected function getModelFillableAttributes($modelClass)
    {
        return (new $modelClass)->getFillable();
    }

    protected function getModelRelationships($modelClass)
    {
        $relationships = [];

        $reflector = new ReflectionClass($modelClass);
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            if ($method->class === $modelClass && $method->isPublic() && !$method->isStatic()) {
                try {
                    $returnType = $method->invoke(new $modelClass);

                    if ($returnType instanceof Relation) {
                        $relatedModel = get_class($returnType->getRelated());
                        $relationName = $method->getName();
                        $foreignKey = $returnType->getForeignKeyName();
                        $relationships[$foreignKey] = class_basename($relatedModel);
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        return $relationships;
    }

    protected function getRelatedModels($modelClass)
    {
        $relatedModels = [];
        $relationships = $this->getModelRelationships($modelClass);
        foreach ($relationships as $relation => $relatedModel) {
            $relatedModels[] = $relatedModel;
        }
        return array_unique($relatedModels);
    }

    protected function getModelCasts($modelClass)
    {
        return (new $modelClass)->getCasts();
    }

    protected function getFakerValueBasedOnCast($cast)
    {
        $fakerMethods = [
            'integer' => '$this->faker->randomNumber()',
            'bigint' => '$this->faker->numberBetween(1, PHP_INT_MAX)',
            'string' => '$this->faker->word',
            'boolean' => '$this->faker->boolean',
            'datetime' => '$this->faker->dateTime()',
            'timestamp' => '$this->faker->dateTime()',
            'date' => '$this->faker->date()',
            'float' => '$this->faker->randomFloat(2, 0, 100)',
            'double' => '$this->faker->randomFloat(2, 0, 100)',
            'decimal' => '$this->faker->randomFloat(2, 0, 100)',
            'array' => '$this->faker->words(3)',
            'json' => 'json_encode($this->faker->words(3))',
            'collection' => 'collect($this->faker->words(3))',
            'object' => '(object) $this->faker->words(3)',
            'uuid' => '$this->faker->uuid',
            'encrypted' => 'encrypt($this->faker->word)',
        ];
    
        return $fakerMethods[$cast] ?? '$this->faker->word';
    }
}
