<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\GeneratorHelpers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeSeederFactoryCommand extends Command
{
    use GeneratorHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:seeder-factory {model : The name of the model} {--fields= : Custom field definitions (e.g., name:string,email:email)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seeder and factory files for a model with automatic field detection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('model');

        // Validate model name
        if (empty($modelName)) {
            $this->error('Model name is required.');

            return 1;
        }

        // Check if model exists
        $modelClass = $this->getModelClass($modelName);
        if (! $modelClass) {
            $this->error("Model {$modelName} does not exist.");

            return 1;
        }

        $this->info("Generating seeder and factory for {$modelName}...");

        // Get fields from migration or custom option
        $fields = $this->getFields($modelClass);

        if (empty($fields)) {
            $this->warn('No fields detected. Using model fillable properties.');
            $fields = $this->getFieldsFromFillable($modelClass);
        }

        if (empty($fields)) {
            $this->error('No fields found. Please specify fields using --fields option.');

            return 1;
        }

        // Detect relationships
        $relationships = $this->detectRelationships($modelClass);

        // Generate factory
        $factoryGenerated = $this->generateFactory($modelName, $fields, $relationships);

        // Generate seeder
        $seederGenerated = $this->generateSeeder($modelName);

        // Display results
        $this->displayResults($factoryGenerated, $seederGenerated, $modelName);

        return 0;
    }

    /**
     * Get fields for the model.
     */
    protected function getFields(string $modelClass): array
    {
        // Check if custom fields provided
        $customFields = $this->option('fields');
        if ($customFields) {
            $parsed = $this->parseFieldsOption($customFields);
            if (! empty($parsed)) {
                return $parsed;
            }
        }

        // Try to get from migration
        $tableName = $this->getTableName($modelClass);
        if ($tableName) {
            $fields = $this->parseFieldsFromMigration($tableName);
            if (! empty($fields)) {
                return $fields;
            }
        }

        // Fallback to fillable
        return $this->getFieldsFromFillable($modelClass);
    }

    /**
     * Generate factory file.
     */
    protected function generateFactory(string $modelName, array $fields, array $relationships): bool
    {
        $factoryPath = database_path("factories/{$modelName}Factory.php");

        // Check if exists
        if (! $this->shouldOverwrite($factoryPath)) {
            return false;
        }

        // Load stub
        $stub = $this->getStub('factory');

        // Build field definitions for factory
        $fieldDefinitions = $this->buildFactoryFields($fields, $relationships);

        // Replace placeholders
        $content = $this->replacePlaceholders($stub, [
            'model' => $modelName,
            'fields' => $fieldDefinitions,
        ]);

        // Write file
        File::put($factoryPath, $content);

        return true;
    }

    /**
     * Build factory field definitions.
     */
    protected function buildFactoryFields(array $fields, array $relationships): string
    {
        $definitions = [];

        foreach ($fields as $name => $type) {
            // Check if this is a foreign key (belongs to relationship)
            $isForeignKey = false;
            foreach ($relationships as $relationMethod => $relationType) {
                if ($relationType === 'BelongsTo' && Str::endsWith($name, '_id')) {
                    // Extract related model name from field name
                    $relatedModel = Str::studly(str_replace('_id', '', $name));
                    $definitions[] = "            '{$name}' => \\App\\Models\\{$relatedModel}::factory(),";
                    $isForeignKey = true;
                    break;
                }
            }

            if (! $isForeignKey) {
                $fakerMethod = $this->mapTypeToFaker($type, $name);
                $definitions[] = "            '{$name}' => {$fakerMethod},";
            }
        }

        return implode("\n", $definitions);
    }

    /**
     * Generate seeder file.
     */
    protected function generateSeeder(string $modelName): bool
    {
        $seederPath = database_path("seeders/{$modelName}Seeder.php");

        // Check if exists
        if (! $this->shouldOverwrite($seederPath)) {
            return false;
        }

        // Load stub
        $stub = $this->getStub('seeder');

        // Replace placeholders
        $content = $this->replacePlaceholders($stub, [
            'model' => $modelName,
            'count' => '10',
        ]);

        // Write file
        File::put($seederPath, $content);

        return true;
    }

    /**
     * Display generation results.
     */
    protected function displayResults(bool $factoryGenerated, bool $seederGenerated, string $modelName): void
    {
        if ($factoryGenerated && $seederGenerated) {
            $this->info('✓ Success! Generated files:');
            $this->line("  - database/factories/{$modelName}Factory.php");
            $this->line("  - database/seeders/{$modelName}Seeder.php");
        } elseif ($factoryGenerated) {
            $this->info('✓ Factory generated:');
            $this->line("  - database/factories/{$modelName}Factory.php");
            $this->warn('  - Seeder skipped (file exists)');
        } elseif ($seederGenerated) {
            $this->info('✓ Seeder generated:');
            $this->line("  - database/seeders/{$modelName}Seeder.php");
            $this->warn('  - Factory skipped (file exists)');
        } else {
            $this->warn('No files generated (all files exist and overwrite declined).');
        }
    }
}
