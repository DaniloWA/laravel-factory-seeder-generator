# Laravel Factory Seeder Generator

## Installation

1. Install the package via Composer:

   ```bash
   composer require your-vendor/laravel-factory-seeder-generator
   ```

## Usage

To generate a factory for a model:

```bash
php artisan make:factory ModelName
```

To generate a seeder for a model:

```bash
php artisan make:seeder ModelName
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Danilowa\LaravelFactorySeederGenerator\Providers\FactorySeederGeneratorServiceProvider"
```

````

### 7. Publicação

1. **Configure o GitHub e Packagist**: Adicione o repositório no GitHub e registre o pacote no Packagist.

2. **Crie e envie uma tag de versão**:
   ```bash
   git add .
   git commit -m "Initial release"
   git tag v1.0.0
   git push --tags
   ```

````
