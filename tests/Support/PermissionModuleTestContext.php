<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Tests\Support;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\PermissionResource;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\RoleResource;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraUser\Models\User;
use PHPUnit\Framework\Assert;

final class PermissionModuleTestContext
{
    /**
     * @param  list<string>  $features
     */
    public static function createCurrentTenant(array $features = []): Model
    {
        $tenant = makeCurrentTestTenant();

        if ( ! $tenant instanceof Model) {
            Assert::fail('Permission module tests require an installed tenant provider.');
        }

        // Every permission feature defaults to active in the real published config, so
        // deactivate whatever wasn't explicitly requested to keep tenant state deterministic.
        $allFeatures = array_map(fn(PermissionFeatureEnum $feature): string => $feature->value, PermissionFeatureEnum::cases());

        Feature::for($tenant)->deactivate(array_values(array_diff($allFeatures, $features)));
        Feature::for($tenant)->activate($features);

        return $tenant;
    }

    public static function setUpFilamentAdminContext(): Model
    {
        $tenant = self::createCurrentTenant([
            PermissionFeatureEnum::ModuleEnabled->value,
            PermissionFeatureEnum::PermissionManagement->value,
            PermissionFeatureEnum::RoleManagement->value,
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
                'username' => 'super-admin',
                'email'    => 'super-admin@example.test',
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
                ->tenant(testTenantModel())
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
}
