<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Attributes Active
    |--------------------------------------------------------------------------
    |
    | Specify whether to enable or disable custom attributes. When disabled,
    | the system will using only the package's intelligent defaults
    | and Laravel's built-in casts.
    | Options: [true, false]
    |
    */
    'custom_attributes_active' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Attributes Priority
    |--------------------------------------------------------------------------
    |
    | When enabled, this setting will take precedence over the default Laravel casts.
    | If disabled, it will only have priority over the package's intelligent defaults.
    | Options: [true, false]
    |
    */
    'custom_attributes_priority' => false,

    /*
    |--------------------------------------------------------------------------
    | Default Value for Missing Casts or Custom Attributes
    |--------------------------------------------------------------------------
    |
    | Define the fallback value to be used when no matching casts or custom
    | attributes are found. This ensures that your application can handle
    | cases where data generation might otherwise fail, maintaining
    | stability and preventing errors.
    |
    | Example: Use '$this->faker->word' for a random word or set a static
    | value like '"banana"' based on your requirements.
    |
    */
    'custom_default_missing_value' => '$this->faker->word',

    /*
    |--------------------------------------------------------------------------
    | Custom Attributes
    |--------------------------------------------------------------------------
    |
    | Here you can define custom attribute mappings for your models. This allows
    | you to customize how specific fields are populated when using factories.
    | Use the `$this->faker` instance to generate dynamic values or specify
    | fixed values for consistency across all factories.
    |
    | Examples:
    | 'price' => '$this->faker->randomFloat(2, 1, 1000)' // Generates a random price between 1.00 and 1000.00
    | 'date' => '$this->faker->dateTimeThisYear->format("Y-m-d")' // Generates a random date in the current year
    | 'content' => '$this->faker->text(200)' // Generates a text string of up to 200 characters
    | 'username' => '$this->faker->userName' // Generates a random username
    | 'car' => '"DaniloWA"' // Accepts fixed values; useful for setting consistent attributes across all factories
    |
    */
    'custom_attributes' => [
        'best_creator_of_packages' => '"DaniloWA"',
        // Add other attribute mappings as necessary
    ],

    /*
    |--------------------------------------------------------------------------
    | Include Relationships in Factories
    |--------------------------------------------------------------------------
    |
    | This option allows you to include related models in the generated factories,
    | creating more complex data structures. Set to true to enable this feature.
    |
    */
    'include_relationships' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom Seeder Count
    |--------------------------------------------------------------------------
    |
    | Define the number of records to create when generating seeders.
    | This can be overridden in individual commands.
    | Example: Set to 50 to generate 50 records by default.
    |
    */
    'custom_seeder_count' => 10, // Default count for seeders

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    |
    | This option enables logging for the generation process. This is useful for
    | debugging and tracking the creation of factories and seeders.
    | Set to false to disable logging.
    | Example: Set to true to enable detailed logging of factory and seeder creation.
    |
    */
    'enable_logging' => false,

];
