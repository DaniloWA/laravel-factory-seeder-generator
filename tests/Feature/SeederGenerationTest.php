<?php

namespace Danilowa\LaravelFactorySeederGenerator\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\TestCase;

class SeederGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->removeGeneratedSeeder('Post');
    }

    protected function tearDown(): void
    {
        $this->removeGeneratedSeeder('Post');

        parent::tearDown();
    }

    public function test_seeder_generation()
    {
        Artisan::call('make:seeder', ['model' => 'Post']);

        $seederPath = base_path('database/seeders/PostSeeder.php');
        $this->assertTrue(File::exists($seederPath), 'Seeder was not created');

        $seederContent = File::get($seederPath);
        $this->assertStringContainsString('class PostSeeder extends Seeder', $seederContent, 'Seeder content is incorrect');
    }

    protected function removeGeneratedSeeder($model)
    {
        $seederPath = base_path("database/seeders/{$model}Seeder.php");

        if (File::exists($seederPath)) {
            File::delete($seederPath);
        }
    }
}
