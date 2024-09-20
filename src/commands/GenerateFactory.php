<?php

namespace Danilowa\LaravelFactorySeederGenerator\Commands;

use ReflectionClass;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Relations\Relation;

class GenerateFactory extends Command
{
    protected $signature = 'make:a-factory {model}';
    protected $description = 'Generate a smart factory for the given model';

    public function handle()
    {
        $modelName = $this->argument('model');
        $modelClass = $this->getModelClass($modelName);

        if ($modelClass === 1) {
            return 1;
        }

        $factoryPath = $this->generateFactory($modelClass);

        $this->info("Factory created at {$factoryPath}");
        return 0;
    }

    protected function getModelClass($modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";
        if (!class_exists($modelClass)) {
            $this->error("Model class {$modelName} does not exist.");
            return 1;
        }

        if (!in_array('Illuminate\\Database\\Eloquent\\Factories\\HasFactory', class_uses($modelClass))) {
            $this->error("Model {$modelClass} does not use the HasFactory trait.");
            return 1;
        }

        return $modelClass;
    }

    protected function generateFactory($modelClass)
    {
        $modelName = class_basename($modelClass);
        $factoryName = "{$modelName}Factory.php";
        $factoryPath = base_path("database/factories/{$factoryName}");

        if (!File::isDirectory(base_path('database/factories'))) {
            File::makeDirectory(base_path('database/factories'), 0755, true);
        }

        if (File::exists($factoryPath)) {
            $this->info("Factory already exists at {$factoryPath}");
            return $factoryPath;
        }

        $factoryContent = $this->generateFactoryContent($modelClass);

        try {
            File::put($factoryPath, $factoryContent);
        } catch (\Exception $e) {
            $this->error("Failed to create factory: {$e->getMessage()}");
            return null;
        }

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
                $factoryContent .= "            '{$attribute}' => " . $this->getFakerValueBasedOnKey($attribute, $casts) . ",\n";
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
        $includeRelationships = Config::get('factorySeederGenerator.include_relationships', true);

        if ($includeRelationships === false) {
            return [];
        }

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
        $includeRelationships = Config::get('factorySeederGenerator.include_relationships', true);

        if ($includeRelationships === false) {
            return [];
        }

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

    protected function getDefaultMappings()
    {
        return [
            'id' => '$this->faker->randomNumber()',
            'at' => '$this->faker->dateTime()',
            'date' => '$this->faker->dateTime()',
            'name' => '$this->faker->name',
            'title' => '$this->faker->name',
            'score' => '$this->faker->randomFloat(1, 1, 5)',
            'likes' => '$this->faker->randomFloat(1, 1, 5)',
            'dislikes' => '$this->faker->randomFloat(1, 1, 5)',
            'rating' => '$this->faker->randomFloat(1, 1, 5)',
            'timestamp' => '$this->faker->dateTime()',
            'status' => '$this->faker->boolean',
            'review' => '$this->faker->text',
            'content' => '$this->faker->text',
            'location' => '$this->faker->city',
            'platform' => '$this->faker->word',
            'language' => '$this->faker->languageCode',
            'verified' => '$this->faker->boolean',
            'bigint' => '$this->faker->numberBetween(1, 1000000)',
            'decimal' => '$this->faker->randomFloat(2, 1, 100)',
            'array' => '$this->faker->words(3)',
            'collection' => '$this->faker->words(3)',
            'json' => 'json_encode($this->faker->words(3))',
            'object' => '(object) $this->faker->words(3)',
            'uuid' => '$this->faker->uuid',
            'email' => '$this->faker->safeEmail',
            'address' => '$this->faker->address',
            'phone' => '$this->faker->phoneNumber',
            'contact' => '$this->faker->phoneNumber',
            'postal' => '$this->faker->postcode',
            'zip' => '$this->faker->postcode',
            'price' => '$this->faker->randomFloat(2, 1, 1000)',
            'time' => '$this->faker->time()',
            'gender' => '$this->faker->randomElement(["male", "female", "other"])',
            'username' => '$this->faker->userName',
            'currency' => '$this->faker->currencyCode',
            'country' => '$this->faker->country',
            'state' => '$this->faker->state',
            'city' => '$this->faker->city',
            'street' => '$this->faker->streetName',
            'company' => '$this->faker->company',
            'job' => '$this->faker->jobTitle',
            'color' => '$this->faker->safeColorName',
            'url' => '$this->faker->url',
            'ipv4' => '$this->faker->ipv4',
            'ipv6' => '$this->faker->ipv6',
            'macAddress' => '$this->faker->macAddress',
            'iban' => '$this->faker->iban',
            'bic' => '$this->faker->swiftBicNumber',
            'licensePlate' => '$this->faker->licensePlate',
            'hexColor' => '$this->faker->hexColor',
            'rgbColor' => '$this->faker->rgbColor',
            'isbn' => '$this->faker->isbn13',
            'extension' => '$this->faker->fileExtension',
            'password' => '$this->faker->password',
            'age' => '$this->faker->numberBetween(0, 100)',
            'birthday' => '$this->faker->dateTimeThisCentury->format("Y-m-d")',
            'sentence' => '$this->faker->sentence',
            'paragraph' => '$this->faker->paragraph',
            'text' => '$this->faker->text',
            'domain' => '$this->faker->domainName',
            'hostname' => '$this->faker->domainWord',
            'boolean' => '$this->faker->boolean',
            'latitude' => '$this->faker->latitude',
            'longitude' => '$this->faker->longitude',
            'word' => '$this->faker->word',
            'words' => '$this->faker->words(5, true)',
            'lines' => '$this->faker->paragraphs(3, true)',
            'month' => '$this->faker->monthName',
            'weekday' => '$this->faker->dayOfWeek',
            'year' => '$this->faker->year',
            'first_name' => '$this->faker->firstName',
            'last_name' => '$this->faker->lastName',
            'colorName' => '$this->faker->safeColorName',
            'safeEmail' => '$this->faker->safeEmail',
            'freeEmail' => '$this->faker->freeEmail',
            'companyEmail' => '$this->faker->companyEmail',
            'pages' => '$this->faker->numberBetween(0, 100)',
            'sha256' => '$this->faker->sha256',
            'md5' => '$this->faker->md5',
            'locale' => '$this->faker->locale',
            'currencyCode' => '$this->faker->currencyCode',
            'swiftBicNumber' => '$this->faker->swiftBicNumber',
            'isbn13' => '$this->faker->isbn13',
            'fileExtension' => '$this->faker->fileExtension',
            'mimeType' => '$this->faker->mimeType',
            'timezone' => '$this->faker->timezone',
            'emoji' => '$this->faker->emoji',
            'bio' => '$this->faker->text(150)',
            'avatar' => '$this->faker->imageUrl(640, 480, "people")',
            'cover_img' => '$this->faker->imageUrl(1280, 720, "nature")',
            'img' => '$this->faker->imageUrl(640, 480)',
            'description' => '$this->faker->text(200)',
            'instagram' => '$this->faker->userName',
            'facebook' => '$this->faker->userName',
            'twitter' => '$this->faker->userName',
            'is_verified' => '$this->faker->boolean',
            'is_public' => '$this->faker->boolean',
            'comments_count' => '$this->faker->numberBetween(0, 100)',
            'count' => '$this->faker->numberBetween(1, 500)',
            'slug' => '$this->faker->slug',
            'is' => '$this->faker->boolean',
        ];

    }

    protected function getSimilarMapping($attribute, $mappings, $customDefaultMissingValue)
    {
        $bonusSuffixes = ['is', 'at', 'name', 'date', 'id', 'count', 'score'];
        $bestMatchingKey = null;
        $highestSimilarityScore = 0;

        $separatedAttributes = explode('_', $attribute);

        foreach ($mappings as $mappingKey => $mappingValue) {
            if ($attribute === $mappingKey) {
                return $mappingValue;
            }

            if ($this->hasPartialMatch($separatedAttributes, $mappingKey)) {
                $similarityScore = $this->calculateSimilarityScore($attribute, $mappingKey, $bonusSuffixes);

                if ($similarityScore > $highestSimilarityScore) {
                    $highestSimilarityScore = $similarityScore;
                    $bestMatchingKey = $mappingKey;
                }
            }
        }

        if ($bestMatchingKey) {
            $this->info("No cast found for '{$attribute}'. Using mapping with key '{$bestMatchingKey}' and value '{$mappings[$bestMatchingKey]}'.");
            return $mappings[$bestMatchingKey];
        }

        $this->warn("No cast or mapping found for '{$attribute}'. Defaulting to a generic word.");
        return $customDefaultMissingValue;
    }

    protected function hasPartialMatch($separatedAttributes, $mappingKey)
    {
        foreach ($separatedAttributes as $part) {
            if (stripos($mappingKey, $part) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function calculateSimilarityScore($attribute, $mappingKey, $bonusSuffixes)
    {
        $similarityScore = 1 - (levenshtein($attribute, $mappingKey) / max(strlen($attribute), strlen($mappingKey)));

        foreach ($bonusSuffixes as $suffix) {
            if (substr($mappingKey, -strlen($suffix)) === $suffix) {
                $similarityScore += 0.1;
                break;
            }
        }

        return $similarityScore;
    }

    protected function getFakerValueBasedOnKey($attribute, $casts)
    {
        $customAttributesActive = Config::get('factorySeederGenerator.custom_attributes_active', false);
        $customAttributesPriority = Config::get('factorySeederGenerator.custom_attributes_priority', false);
        $customAttributes = Config::get('factorySeederGenerator.custom_attributes', []);
        $customDefaultMissingValue = Config::get('factorySeederGenerator.custom_default_missing_value', '$this->faker->word');

        if ($customAttributesActive && array_key_exists($attribute, $customAttributes)) {
            return $customAttributes[$attribute];
        }

        $checks = $customAttributesPriority
            ? [$customAttributes, $casts]
            : [$casts, $customAttributes];

        foreach ($checks as $source) {
            if (array_key_exists($attribute, $source)) {
                return $source === $customAttributes
                    ? $customAttributes[$attribute]
                    : $this->getFakerValueBasedOnCast($source[$attribute]);
            }
        }

        $mappings = $this->getDefaultMappings();

        if (array_key_exists($attribute, $mappings)) {
            $this->info("Exact match found for '{$attribute}'. Using mapping with value '{$mappings[$attribute]}'.");
            return $mappings[$attribute];
        }

        return $this->getSimilarMapping($attribute, $mappings, $customDefaultMissingValue);
    }

    protected function getFakerValueBasedOnCast($cast)
    {
        $defaultCast = '$this->faker->word';
        $fakerMethods = [
            'array' => '$this->faker->words(3)',
            'string' => '$this->faker->word',
            'boolean' => '$this->faker->boolean',
            'collection' => 'collect($this->faker->words(3))',
            'date' => '$this->faker->date()',
            'datetime' => '$this->faker->dateTime()',
            'immutable_date' => '$this->faker->dateTime()->format("Y-m-d")',
            'immutable_datetime' => '$this->faker->dateTime()',
            'decimal' => '$this->faker->randomFloat(2, 0, 100)',
            'double' => '$this->faker->randomFloat(2, 0, 100)',
            'encrypted' => 'encrypt($this->faker->word)',
            'encrypted:array' => 'encrypt(json_encode($this->faker->words(3)))',
            'encrypted:collection' => 'encrypt(collect($this->faker->words(3)))',
            'encrypted:object' => 'encrypt((object) $this->faker->words(3))',
            'float' => '$this->faker->randomFloat(2, 0, 100)',
            'hashed' => 'Hash::make($this->faker->word)',
            'integer' => '$this->faker->randomNumber()',
            'object' => '(object) $this->faker->words(3)',
            'real' => '$this->faker->randomFloat(2, 0, 100)',
            'timestamp' => '$this->faker->dateTime()->getTimestamp()',
        ];

        return $fakerMethods[$cast] ?? $defaultCast;
    }

}
