<?php

namespace Danilowa\LaravelFactorySeederGenerator\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\TestCase;

class FactoryGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->removeGeneratedFactory('Post');
    }

    protected function tearDown(): void
    {
        $this->removeGeneratedFactory('Post');

        parent::tearDown();
    }

    public function test_factory_generation()
    {
        Artisan::call('make:factory', ['model' => 'Post']);

        $factoryPath = base_path('database/factories/PostFactory.php');
        $this->assertTrue(File::exists($factoryPath), 'Factory was not created');

        $factoryContent = File::get($factoryPath);
        $this->assertStringContainsString('class PostFactory extends Factory', $factoryContent, 'Factory content is incorrect');
    }

    protected function removeGeneratedFactory($model)
    {
        $factoryPath = base_path("database/factories/{$model}Factory.php");

        if (File::exists($factoryPath)) {
            File::delete($factoryPath);
        }
    }
}
