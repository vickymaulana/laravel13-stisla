<?php

namespace App\Console\Commands\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait GeneratorHelpers
{
    /**
     * Check if model exists and return the full class name.
     */
    protected function getModelClass(string $modelName): ?string
    {
        $modelClass = 'App\\Models\\'.Str::studly($modelName);

        if (class_exists($modelClass)) {
            return $modelClass;
        }

        return null;
    }

    /**
     * Parse migration files to extract field definitions.
     */
    protected function parseFieldsFromMigration(string $tableName): array
    {
        $migrationPath = database_path('migrations');
        $files = File::glob($migrationPath.'/*_create_'.$tableName.'_table.php');

        if (empty($files)) {
            return [];
        }

        $migrationFile = $files[0];
        $content = File::get($migrationFile);

        $fields = [];

        // Extract column definitions
        preg_match_all('/\$table->(\w+)\([\'"](\w+)[\'"]/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $type = $match[1];
            $name = $match[2];

            // Skip timestamp and id fields
            if (in_array($name, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $fields[$name] = $type;
        }

        return $fields;
    }

    /**
     * Map database column type to Faker method.
     */
    protected function mapTypeToFaker(string $type, string $fieldName): string
    {
        // Check field name patterns first
        if (Str::contains($fieldName, ['email'])) {
            return 'fake()->safeEmail()';
        }
        if (Str::contains($fieldName, ['phone', 'mobile'])) {
            return 'fake()->phoneNumber()';
        }
        if (Str::contains($fieldName, ['address'])) {
            return 'fake()->address()';
        }
        if (Str::contains($fieldName, ['city'])) {
            return 'fake()->city()';
        }
        if (Str::contains($fieldName, ['country'])) {
            return 'fake()->country()';
        }
        if (Str::contains($fieldName, ['name', 'title'])) {
            return 'fake()->name()';
        }
        if (Str::contains($fieldName, ['description', 'content', 'body'])) {
            return 'fake()->paragraph()';
        }

        // Map by type
        return match ($type) {
            'string', 'varchar' => 'fake()->sentence(3)',
            'text', 'longText', 'mediumText' => 'fake()->paragraph()',
            'integer', 'bigInteger', 'unsignedBigInteger', 'unsignedInteger' => 'fake()->numberBetween(1, 100)',
            'decimal', 'float', 'double' => 'fake()->randomFloat(2, 0, 1000)',
            'boolean', 'tinyInteger' => 'fake()->boolean()',
            'date' => 'fake()->date()',
            'datetime', 'timestamp' => 'fake()->dateTime()',
            'time' => 'fake()->time()',
            'json' => '[]',
            default => 'fake()->word()',
        };
    }

    /**
     * Map database column type to HTML input type.
     */
    protected function mapTypeToInputType(string $type, string $fieldName): string
    {
        // Check field name patterns first
        if (Str::contains($fieldName, ['email'])) {
            return 'email';
        }
        if (Str::contains($fieldName, ['password'])) {
            return 'password';
        }
        if (Str::contains($fieldName, ['phone', 'mobile'])) {
            return 'tel';
        }
        if (Str::contains($fieldName, ['url', 'website'])) {
            return 'url';
        }

        // Map by type
        return match ($type) {
            'text', 'longText', 'mediumText' => 'textarea',
            'integer', 'bigInteger', 'unsignedBigInteger', 'unsignedInteger' => 'number',
            'decimal', 'float', 'double' => 'number',
            'boolean', 'tinyInteger' => 'checkbox',
            'date' => 'date',
            'datetime', 'timestamp' => 'datetime-local',
            'time' => 'time',
            default => 'text',
        };
    }

    /**
     * Check if file exists and confirm overwrite.
     */
    protected function shouldOverwrite(string $path): bool
    {
        if (! File::exists($path)) {
            return true;
        }

        return $this->confirm("File {$path} already exists. Overwrite?");
    }

    /**
     * Load stub template from project stubs or default.
     */
    protected function getStub(string $stubName): string
    {
        $projectStub = base_path("stubs/{$stubName}.stub");

        if (File::exists($projectStub)) {
            return File::get($projectStub);
        }

        // Fallback to default stub (should exist since we created them)
        return File::get($projectStub);
    }

    /**
     * Replace placeholders in stub content.
     */
    protected function replacePlaceholders(string $stub, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $stub = str_replace("{{ {$key} }}", $value, $stub);
        }

        return $stub;
    }

    /**
     * Get field definitions from fillable array if migration not found.
     */
    protected function getFieldsFromFillable(string $modelClass): array
    {
        if (! class_exists($modelClass)) {
            return [];
        }

        $model = new $modelClass;
        $fillable = $model->getFillable();

        // Return fields with default string type
        return array_fill_keys($fillable, 'string');
    }

    /**
     * Parse custom fields option string.
     */
    protected function parseFieldsOption(?string $fieldsOption): array
    {
        if (empty($fieldsOption)) {
            return [];
        }

        $fields = [];
        $fieldPairs = explode(',', $fieldsOption);

        foreach ($fieldPairs as $pair) {
            if (str_contains($pair, ':')) {
                [$name, $type] = explode(':', trim($pair));
                $fields[trim($name)] = trim($type);
            }
        }

        return $fields;
    }

    /**
     * Get table name from model class.
     */
    protected function getTableName(string $modelClass): string
    {
        if (! class_exists($modelClass)) {
            return '';
        }

        $model = new $modelClass;

        return $model->getTable();
    }

    /**
     * Detect model relationships using reflection.
     */
    protected function detectRelationships(string $modelClass): array
    {
        if (! class_exists($modelClass)) {
            return [];
        }

        $reflection = new \ReflectionClass($modelClass);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $relationships = [];

        foreach ($methods as $method) {
            $returnType = $method->getReturnType();

            if (! $returnType) {
                continue;
            }

            $returnTypeName = $returnType->getName();

            // Check if return type is a relationship
            if (Str::contains($returnTypeName, 'Illuminate\Database\Eloquent\Relations')) {
                $relationType = class_basename($returnTypeName);
                $relationships[$method->getName()] = $relationType;
            }
        }

        return $relationships;
    }
}
