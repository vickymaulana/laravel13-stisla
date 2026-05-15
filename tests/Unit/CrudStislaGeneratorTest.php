<?php

namespace Tests\Unit;

use App\Console\Commands\MakeCrudStislaCommand;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class CrudStislaGeneratorTest extends TestCase
{
    public function test_generated_fields_use_model_variable_instead_of_first_field_name(): void
    {
        $command = new MakeCrudStislaCommand;
        $fields = ['title' => 'string', 'is_active' => 'boolean'];

        $tableColumns = $this->invoke($command, 'buildTableColumns', [$fields, 'Product']);
        $formFields = $this->invoke($command, 'buildFormFields', [$fields, true, 'Product']);
        $displayFields = $this->invoke($command, 'buildDisplayFields', [$fields, 'Product']);

        $this->assertStringContainsString('$product->title', $tableColumns);
        $this->assertStringNotContainsString('$title->title', $tableColumns);
        $this->assertStringContainsString('$product->title', $formFields);
        $this->assertStringNotContainsString('$title->title', $formFields);
        $this->assertStringContainsString('$product->is_active', $displayFields);
        $this->assertStringNotContainsString('$title->is_active', $displayFields);
    }

    public function test_generated_markup_uses_bootstrap_five_conventions(): void
    {
        $command = new MakeCrudStislaCommand;

        $checkbox = $this->invoke($command, 'buildFormFields', [['is_active' => 'boolean'], true, 'Product']);
        $displayFields = $this->invoke($command, 'buildDisplayFields', [['is_active' => 'boolean'], 'Product']);
        $indexStub = file_get_contents(__DIR__.'/../../stubs/view-index-stisla.stub');

        $this->assertStringContainsString('form-check', $checkbox);
        $this->assertStringContainsString('form-check-input', $checkbox);
        $this->assertStringNotContainsString('custom-control', $checkbox);
        $this->assertStringContainsString('badge bg-success', $displayFields);
        $this->assertStringContainsString('badge bg-secondary', $displayFields);
        $this->assertStringNotContainsString('badge-success', $displayFields);
        $this->assertStringContainsString('data-bs-dismiss="alert"', $indexStub);
        $this->assertStringNotContainsString('data-dismiss="alert"', $indexStub);
    }

    public function test_generated_resource_route_is_auth_protected(): void
    {
        $command = new MakeCrudStislaCommand;

        $route = $this->invoke($command, 'buildRouteDefinition', ['Product']);

        $this->assertStringContainsString("Route::middleware('auth')->group(function () {", $route);
        $this->assertStringContainsString("Route::resource('products', App\\Http\\Controllers\\ProductController::class);", $route);
        $this->assertStringContainsString('});', $route);
    }

    /**
     * @param array<int, mixed> $arguments
     */
    private function invoke(MakeCrudStislaCommand $command, string $method, array $arguments): mixed
    {
        $reflection = new ReflectionMethod($command, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($command, $arguments);
    }
}
