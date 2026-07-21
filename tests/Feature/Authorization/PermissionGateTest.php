<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The admin views load compiled assets via @vite; stub it so tests do
        // not require a frontend build to render a successful response.
        $this->withoutVite();

        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        foreach (['manage-settings', 'manage-users'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertForbidden();
    }

    public function test_user_with_granular_permission_is_allowed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->givePermissionTo('manage-settings');

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk();
    }

    public function test_superadmin_role_is_allowed_without_explicit_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('superadmin');

        $this->actingAs($admin)
            ->get(route('settings.index'))
            ->assertOk();
    }

    public function test_granular_permission_is_scoped_to_its_own_area(): void
    {
        // A user granted manage-settings must not reach the user-management area.
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->givePermissionTo('manage-settings');

        $this->actingAs($user)
            ->get(route('hakakses.index'))
            ->assertForbidden();
    }
}
