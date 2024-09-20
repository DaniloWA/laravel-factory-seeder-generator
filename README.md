# Laravel Factory Seeder Generator

## Installation

1. Install the package via Composer:

   ```bash
   composer require danilowa/laravel-factory-seeder-generator
   ```

## Usage

To generate a factory for a model:

```bash
php artisan make:a-factory ModelName
```

To generate a seeder for a model:

```bash
php artisan make:a-seeder ModelName
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Danilowa\LaravelFactorySeederGenerator\Providers\FactorySeederGeneratorServiceProvider"
```
