<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\PermissionResource;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\RoleResource;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;
use Misaf\VendraUser\Models\User;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

pest()->extend(Misaf\VendraPermission\Tests\TestCase::class)->in('Feature');

pest()->extend(Misaf\VendraPermission\Tests\TestCase::class)->in('Unit');

/**
 * @param list<string> $features
 */
function createCurrentTenantForPermissionModule(array $features = []): Tenant
{
    $tenant = Tenant::factory()->enabled()->create();

    $tenant->makeCurrent();

    Feature::for($tenant)->activate($features);

    return $tenant;
}

function setUpFilamentAdminContextForPermissionModule(): Tenant
{
    $tenant = createCurrentTenantForPermissionModule([
        PermissionFeatureEnum::MODULE_ENABLED->value,
        PermissionFeatureEnum::PERMISSION_MANAGEMENT->value,
        PermissionFeatureEnum::ROLE_MANAGEMENT->value,
    ]);

    $superAdminRole = Role::factory()
        ->forTenant($tenant)
        ->forGuard('web')
        ->create([
            'name' => Config::string('vendra-permission.super_admin_role', 'super-admin'),
        ]);

    $user = User::factory()
        ->forTenant($tenant)
        ->create([
            'username'  => 'super-admin',
            'email'     => 'super-admin@example.test',
        ]);

    $user->assignRole($superAdminRole);

    app(PanelRegistry::class)->register(
        Panel::make()
            ->default()
            ->id('admin')
            ->path('admin')
            ->resources([
                PermissionResource::class,
                RoleResource::class,
            ])
            ->tenant(Tenant::class)
    );

    Table::configureUsing(function (Table $table) {
        return $table
            ->paginationPageOptions([10, 25, 50])
            ->deferLoading();
    });

    Filament::setCurrentPanel('admin');
    Livewire::actingAs($user);
    Filament::setTenant($tenant);
    Filament::bootCurrentPanel();

    app('url')->resolveMissingNamedRoutesUsing(static fn(): string => '/');

    return $tenant;
}
