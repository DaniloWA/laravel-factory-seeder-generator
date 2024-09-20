<p align="center">
  <img src="https://img.shields.io/packagist/v/danilowa/laravel-factory-seeder-generator" alt="Latest Version" />
  <img src="https://img.shields.io/packagist/dt/danilowa/laravel-factory-seeder-generator" alt="Total Downloads" />
</p>

<h1 align="center">
  <strong>Optimize Your Database Seeding with Laravel Factory Seeder Generator!</strong>
</h1>

<p align="center">
  This package simplifies the creation of structured factories and seeders in Laravel applications, allowing developers to generate consistent and realistic test data with ease. It intelligently handles model attributes, including customizable data generation options and smart handling of casts. With features for logging, error handling, and extensive configuration options, this tool is essential for enhancing productivity and maintaining organized code in your development workflow.
</p>

## 📚 Index

- [Overview](#-overview)
- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [How It Works](#-how-it-works)
- [Configuration](#-configuration)
- [Command Overview](#-command-overview)
  - [Usage Examples](#-usage-examples)
- [Advantages](#-advantages)
- [Conclusion](#-conclusion)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

## 🔍 Overview

The **Laravel Factory Seeder Generator** is an advanced tool designed to streamline the generation of **factories** and **seeders** within Laravel applications. This package automates the creation of seeding files, significantly enhancing productivity and reducing boilerplate code during the development process.

## ⭐ Features

- **Automatic Generation**: Effortlessly create factories and seeders for all your models, eliminating repetitive tasks and allowing for faster development cycles.
- **Intelligent Data Filling**: Automatically extracts all `$fillable` attributes from your models, ensuring that the correct fields are utilized for data generation.
- **Smart Handling of Casts**: Utilizes defined casts in your model to produce realistic data with Laravel's Faker. If no casts are specified, it intelligently infers the most suitable cast for each `$fillable` field, optimizing data generation.
- **Seamless Integration**: Integrates effortlessly with your existing Laravel project structure, requiring minimal configuration and setup.
- **Customizable Options**: Provides flexible configuration settings to tailor the generator to your specific development requirements.

## ✅ Requirements

To use the **Laravel Factory Seeder Generator**, ensure that your environment meets the following requirements:

- **Laravel**: This package requires Laravel 8.x or later.
- **PHP**: Ensure that you are running PHP 8.1 or higher.
- **Faker**: The package leverages the Faker library for data generation, which is included with Laravel.

## 🚀 Installation

To install the **Laravel Factory Seeder Generator**, follow these steps:

1. **Require the Package**: Use Composer to add the package to your Laravel project:

   ```bash
   composer require danilowa/laravel-factory-seeder-generator
   ```

2. **Publish Configuration (optional)**: If your package includes configuration files, you can publish them to your application's config directory:

   ```bash
   php artisan vendor:publish --provider="Danilowa\LaravelFactorySeederGenerator\Providers\FactorySeederGeneratorServiceProvider"
   ```

## 🔧 How It Works

1. **Field Retrieval**: The package scans your models to identify all fields listed in `$fillable`, garantindo que apenas os campos pretendidos sejam utilizados para geração de dados.

2. **Intelligent Data Generation**: For each field in `$fillable`, the package generates relevant data using Laravel's Faker library.

3. **Utilization of Casts**: If your model specifies casts, the package automatically generates suitable data types.

## ⚙️ Configuration

The configuration file allows you to customize the behavior of the generator. Here are the key settings you can modify:

- **Custom Attributes Active**: Toggle this option to enable or disable custom attributes.

  ```php
  'custom_attributes_active' => true,
  ```

- **Default Attributes Priority**: When enabled, this setting takes precedence over the default Laravel casts.

  ```php
  'custom_attributes_priority' => false,
  ```

- **Custom Attributes**: Specify mappings for custom attributes.

  ```php
  'custom_attributes' => [
      'price' => '$this->faker->randomFloat(2, 1, 1000)',
      'username' => '$this->faker->userName',
  ],
  ```

- **Include Relationships in Factories**: This option allows you to include related models in the generated factories.

  ```php
  'include_relationships' => true,
  ```

- **Custom Seeder Count**: Set the default number of records to create when generating seeders.

  ```php
  'custom_seeder_count' => 10,
  ```

- **Enable Logging**: This option enables detailed logging for the generation process.

  ```php
  'enable_logging' => false,
  ```

## ⚡ Command Overview

The package includes a variety of commands to facilitate the factory and seeder generation process:

- **GenerateFactory**: Generates a factory for a specified model.
- **GenerateSeeder**: Creates a seeder for a specified model.
- **GenerateFactoryAndSeederCommand**: Combines functionalities of both commands.
- **GenerateFactoryAndSeederAllCommand**: Automatically generates factories and seeders for all models in your application.

### 📜 Usage Examples

To generate all necessary files for models without factories and seeders, run:

```bash
php artisan make:a-all-factories-and-seeders --count=12
```

To generate factory and seeder files for specific models, use:

```bash
php artisan make:a-factories-and-seeders --count=14
```

To generate a factory and seeder for a particular model, execute:

```bash
php artisan make:a-factory {ModelName}
php artisan make:a-seeder {ModelName} --count=15
```

## 🚀 Advantages

- **Efficiency**: Reduces the time spent on manual factory and seeder creation.
- **Consistency**: Ensures that generated data is valid and consistent.
- **Flexibility**: Customizable settings to adapt the package to your unique development needs.

## ✅ Conclusion

The **Laravel Factory Seeder Generator** is an essential tool for Laravel developers looking to optimize their workflow by automating factory and seeder creation.

## 🤝 Contributing

Contributions are welcome! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Make your changes and commit them (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Open a pull request.

## 📝 License

This package is licensed under the MIT License.

## 📬 Contact

For any questions or feedback, please reach out to:

- **Danilo Oliveira:** [daniloworkdev@gmail.com](mailto:daniloworkdev@gmail.com)
- **Website:** [daniloo.dev](http://www.daniloo.dev)

---

**Note:** This package is currently under development.
