<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\GeneratorHelpers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrudStislaCommand extends Command
{
    use GeneratorHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud-stisla {model : The name of the model} {--fields= : Custom field definitions} {--except= : Fields to exclude}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRUD controller and Stisla views for a model';

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

        $this->info("Generating CRUD for {$modelName}...");

        // Get fields
        $fields = $this->getFields($modelClass);

        if (empty($fields)) {
            $this->error('No fields found. Please specify fields using --fields option.');

            return 1;
        }

        // Apply field exclusions
        $fields = $this->applyExclusions($fields);

        $generated = [];

        // Generate controller
        if ($this->generateController($modelName, $fields)) {
            $generated[] = "app/Http/Controllers/{$modelName}Controller.php";
        }

        // Generate views
        $viewsGenerated = $this->generateViews($modelName, $fields);
        $generated = array_merge($generated, $viewsGenerated);

        // Append routes
        if ($this->appendRoutes($modelName)) {
            $generated[] = 'routes/web.php (route appended)';
        }

        // Display results
        $this->displayResults($generated, $modelName);

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
     * Apply field exclusions.
     */
    protected function applyExclusions(array $fields): array
    {
        $except = $this->option('except');
        if (empty($except)) {
            return $fields;
        }

        $excludeFields = array_map('trim', explode(',', $except));

        foreach ($excludeFields as $field) {
            unset($fields[$field]);
        }

        return $fields;
    }

    /**
     * Generate controller file.
     */
    protected function generateController(string $modelName, array $fields): bool
    {
        $controllerPath = app_path("Http/Controllers/{$modelName}Controller.php");

        if (! $this->shouldOverwrite($controllerPath)) {
            return false;
        }

        $stub = $this->getStub('controller-stisla');

        // Build validation rules
        $validation = $this->buildValidationRules($fields);

        // Prepare replacements
        $modelVariable = Str::camel($modelName);
        $routeName = Str::kebab(Str::plural($modelName));
        $viewPath = Str::kebab(Str::plural($modelName));

        $content = $this->replacePlaceholders($stub, [
            'model' => $modelName,
            'modelVariable' => $modelVariable,
            'routeName' => $routeName,
            'viewPath' => $viewPath,
            'validation' => $validation,
        ]);

        File::put($controllerPath, $content);

        return true;
    }

    /**
     * Build validation rules for fields.
     */
    protected function buildValidationRules(array $fields): string
    {
        $rules = [];

        foreach ($fields as $name => $type) {
            $rule = match ($type) {
                'string', 'varchar' => 'required|string|max:255',
                'text', 'longText' => 'required|string',
                'integer', 'bigInteger' => 'required|integer',
                'decimal', 'float' => 'required|numeric',
                'boolean' => 'required|boolean',
                'date' => 'required|date',
                'datetime' => 'required|date',
                default => 'required',
            };

            $rules[] = "            '{$name}' => '{$rule}',";
        }

        return implode("\n", $rules);
    }

    /**
     * Generate view files.
     */
    protected function generateViews(string $modelName, array $fields): array
    {
        $generated = [];
        $viewPath = Str::kebab(Str::plural($modelName));
        $viewDir = resource_path("views/{$viewPath}");

        // Create directory if not exists
        if (! File::isDirectory($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
        }

        // Generate index view
        if ($this->generateIndexView($modelName, $fields, $viewDir)) {
            $generated[] = "resources/views/{$viewPath}/index.blade.php";
        }

        // Generate create view
        if ($this->generateCreateView($modelName, $fields, $viewDir)) {
            $generated[] = "resources/views/{$viewPath}/create.blade.php";
        }

        // Generate edit view
        if ($this->generateEditView($modelName, $fields, $viewDir)) {
            $generated[] = "resources/views/{$viewPath}/edit.blade.php";
        }

        // Generate show view
        if ($this->generateShowView($modelName, $fields, $viewDir)) {
            $generated[] = "resources/views/{$viewPath}/show.blade.php";
        }

        return $generated;
    }

    /**
     * Generate index view.
     */
    protected function generateIndexView(string $modelName, array $fields, string $viewDir): bool
    {
        $viewPath = $viewDir.'/index.blade.php';

        if (! $this->shouldOverwrite($viewPath)) {
            return false;
        }

        $stub = $this->getStub('view-index-stisla');

        // Build table headers and columns
        $tableHeaders = $this->buildTableHeaders($fields);
        $tableColumns = $this->buildTableColumns($fields, $modelName);

        $content = $this->replacePlaceholders($stub, [
            'modelTitle' => Str::plural($modelName),
            'modelVariable' => Str::camel($modelName),
            'routeName' => Str::kebab(Str::plural($modelName)),
            'tableHeaders' => $tableHeaders,
            'tableColumns' => $tableColumns,
            'columnCount' => count($fields) + 1,
        ]);

        File::put($viewPath, $content);

        return true;
    }

    /**
     * Build table headers.
     */
    protected function buildTableHeaders(array $fields): string
    {
        $headers = [];
        foreach (array_keys($fields) as $field) {
            $headers[] = '                                        <th>'.Str::title(str_replace('_', ' ', $field)).'</th>';
        }

        return implode("\n", $headers);
    }

    /**
     * Build table columns.
     */
    protected function buildTableColumns(array $fields, ?string $modelName = null): string
    {
        $modelVariable = Str::camel($modelName ?? 'item');
        $columns = [];

        foreach (array_keys($fields) as $field) {
            $columns[] = "                                            <td>{{ \${$modelVariable}->{$field} }}</td>";
        }

        return implode("\n", $columns);
    }

    /**
     * Generate create view.
     */
    protected function generateCreateView(string $modelName, array $fields, string $viewDir): bool
    {
        $viewPath = $viewDir.'/create.blade.php';

        if (! $this->shouldOverwrite($viewPath)) {
            return false;
        }

        $stub = $this->getStub('view-create-stisla');

        // Build form fields
        $formFields = $this->buildFormFields($fields, false, $modelName);

        $content = $this->replacePlaceholders($stub, [
            'modelTitle' => $modelName,
            'routeName' => Str::kebab(Str::plural($modelName)),
            'formFields' => $formFields,
        ]);

        File::put($viewPath, $content);

        return true;
    }

    /**
     * Generate edit view.
     */
    protected function generateEditView(string $modelName, array $fields, string $viewDir): bool
    {
        $viewPath = $viewDir.'/edit.blade.php';

        if (! $this->shouldOverwrite($viewPath)) {
            return false;
        }

        $stub = $this->getStub('view-edit-stisla');

        // Build form fields with values
        $formFields = $this->buildFormFields($fields, true, $modelName);

        $content = $this->replacePlaceholders($stub, [
            'modelTitle' => $modelName,
            'modelVariable' => Str::camel($modelName),
            'routeName' => Str::kebab(Str::plural($modelName)),
            'formFields' => $formFields,
        ]);

        File::put($viewPath, $content);

        return true;
    }

    /**
     * Generate show view.
     */
    protected function generateShowView(string $modelName, array $fields, string $viewDir): bool
    {
        $viewPath = $viewDir.'/show.blade.php';

        if (! $this->shouldOverwrite($viewPath)) {
            return false;
        }

        $stub = $this->getStub('view-show-stisla');

        // Build display fields
        $displayFields = $this->buildDisplayFields($fields, $modelName);

        $content = $this->replacePlaceholders($stub, [
            'modelTitle' => $modelName,
            'modelVariable' => Str::camel($modelName),
            'routeName' => Str::kebab(Str::plural($modelName)),
            'displayFields' => $displayFields,
        ]);

        File::put($viewPath, $content);

        return true;
    }

    /**
     * Build form fields for create/edit views.
     */
    protected function buildFormFields(array $fields, bool $withValue = false, ?string $modelName = null): string
    {
        $formFields = [];
        $modelVariable = Str::camel($modelName ?? 'item');

        foreach ($fields as $name => $type) {
            $label = Str::title(str_replace('_', ' ', $name));
            $inputType = $this->mapTypeToInputType($type, $name);
            $value = $withValue ? " value=\"{{ \${$modelVariable}->{$name} }}\"" : '';

            if ($inputType === 'textarea') {
                $value = $withValue ? "{{ \${$modelVariable}->{$name} }}" : '';
                $formFields[] = "                            <div class=\"form-group\">\n".
                               "                                <label>{$label}</label>\n".
                               "                                <textarea name=\"{$name}\" class=\"form-control @error('{$name}') is-invalid @enderror\" rows=\"3\">{$value}</textarea>\n".
                               "                                @error('{$name}')\n".
                               "                                    <div class=\"invalid-feedback\">{{ \$message }}</div>\n".
                               "                                @enderror\n".
                               "                            </div>\n";
            } elseif ($inputType === 'checkbox') {
                $checked = $withValue ? " {{ \${$modelVariable}->{$name} ? 'checked' : '' }}" : '';
                $formFields[] = "                            <div class=\"form-group\">\n".
                               "                                <input type=\"hidden\" name=\"{$name}\" value=\"0\">\n".
                               "                                <div class=\"form-check\">\n".
                               "                                    <input type=\"checkbox\" name=\"{$name}\" value=\"1\" class=\"form-check-input @error('{$name}') is-invalid @enderror\" id=\"{$name}\"{$checked}>\n".
                               "                                    <label class=\"form-check-label\" for=\"{$name}\">{$label}</label>\n".
                               "                                    @error('{$name}')\n".
                               "                                        <div class=\"invalid-feedback\">{{ \$message }}</div>\n".
                               "                                    @enderror\n".
                               "                                </div>\n".
                               "                            </div>\n";
            } else {
                $step = ($inputType === 'number' && in_array($type, ['decimal', 'float', 'double'])) ? ' step="0.01"' : '';
                $formFields[] = "                            <div class=\"form-group\">\n".
                               "                                <label>{$label}</label>\n".
                               "                                <input type=\"{$inputType}\" name=\"{$name}\" class=\"form-control @error('{$name}') is-invalid @enderror\"{$value}{$step}>\n".
                               "                                @error('{$name}')\n".
                               "                                    <div class=\"invalid-feedback\">{{ \$message }}</div>\n".
                               "                                @enderror\n".
                               "                            </div>\n";
            }
        }

        return implode("\n", $formFields);
    }

    /**
     * Build display fields for show view.
     */
    protected function buildDisplayFields(array $fields, ?string $modelName = null): string
    {
        $displayFields = [];
        $modelVariable = Str::camel($modelName ?? 'item');

        foreach ($fields as $name => $type) {
            $label = Str::title(str_replace('_', ' ', $name));

            if (in_array($type, ['date', 'datetime', 'timestamp'])) {
                $value = "{{ \${$modelVariable}->{$name} ? \${$modelVariable}->{$name}->format('Y-m-d H:i') : '-' }}";
            } elseif (in_array($type, ['boolean', 'tinyInteger'])) {
                $value = "@if(\${$modelVariable}->{$name})<span class=\"badge bg-success\">Yes</span>@else<span class=\"badge bg-secondary\">No</span>@endif";
            } else {
                $value = "{{ \${$modelVariable}->{$name} ?? '-' }}";
            }

            $displayFields[] = "                        <div class=\"form-group row\">\n".
                              "                            <label class=\"col-sm-3 col-form-label font-weight-bold\">{$label}:</label>\n".
                              "                            <div class=\"col-sm-9\">\n".
                              "                                <p class=\"form-control-plaintext\">{$value}</p>\n".
                              "                            </div>\n".
                              '                        </div>';
        }

        return implode("\n", $displayFields);
    }

    /**
     * Append routes to web.php.
     */
    protected function appendRoutes(string $modelName): bool
    {
        $routePath = base_path('routes/web.php');
        $content = File::get($routePath);

        $routeName = Str::kebab(Str::plural($modelName));

        // Check if route already exists
        if (Str::contains($content, "'{$routeName}'") || Str::contains($content, "\"{$routeName}\"")) {
            $this->warn("Route for {$routeName} already exists in web.php. Skipping route generation.");

            return false;
        }

        $route = $this->buildRouteDefinition($modelName);

        File::append($routePath, $route);

        return true;
    }

    /**
     * Build an auth-protected resource route for the generated CRUD.
     */
    protected function buildRouteDefinition(string $modelName): string
    {
        $routeName = Str::kebab(Str::plural($modelName));

        return "\n// Auto-generated routes for {$modelName}\n".
               "Route::middleware('auth')->group(function () {\n".
               "    Route::resource('{$routeName}', App\\Http\\Controllers\\{$modelName}Controller::class);\n".
               "});\n";
    }

    /**
     * Display generation results.
     */
    protected function displayResults(array $generated, string $modelName): void
    {
        if (empty($generated)) {
            $this->warn('No files generated (all files exist and overwrite declined).');

            return;
        }

        $this->info('✓ Success! Generated files:');
        foreach ($generated as $file) {
            $this->line("  - {$file}");
        }

        $this->newLine();
        $this->info('Next steps:');
        $this->line('  1. Run migrations if not done: php artisan migrate');
        $this->line('  2. Test your CRUD at: /'.Str::kebab(Str::plural($modelName)));
    }
}
