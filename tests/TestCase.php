<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Tests;

use Awcodes\BadgeableColumn\BadgeableColumnServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Support\Facades\Http;
use Livewire\LivewireServiceProvider;
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraPermission\PermissionServiceProvider;
use Mokhosh\FilamentJalali\FilamentJalaliServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Override;
use Spatie\Multitenancy\MultitenancyServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected int $tenantId;

    #[Override]
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        TextColumn::macro('jalaliDateTime', function (string $format = 'Y-m-d H:i', bool $toLatin = false): TextColumn {
            return $this->dateTime($format);
        });

        Http::preventStrayRequests();
    }

    #[Override]
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        $app['config']->set('app.locale', 'en');
        $app['config']->set('app.fallback_locale', 'en');

        $app['config']->set('pennant.default', 'array');
        $app['config']->set('pennant.stores.array', [
            'driver' => 'array',
        ]);

        $app['config']->set('permission.models.permission', Permission::class);
        $app['config']->set('permission.models.role', Role::class);
        $app['config']->set('permission.cache.key', 'spatie.permission.cache');
        $app['config']->set('permission.cache.store', 'array');
        $app['config']->set('permission.column_names.role_pivot_key', 'role_id');
        $app['config']->set('permission.column_names.permission_pivot_key', 'permission_id');
        $app['config']->set('permission.column_names.model_morph_key', 'model_id');
        $app['config']->set('permission.table_names.permissions', 'permissions');
        $app['config']->set('permission.table_names.roles', 'roles');
        $app['config']->set('permission.table_names.model_has_permissions', 'model_has_permissions');
        $app['config']->set('permission.table_names.model_has_roles', 'model_has_roles');
        $app['config']->set('permission.table_names.role_has_permissions', 'role_has_permissions');
        $app['config']->set('permission.teams', false);

        $app['config']->set('activitylog.default_auth_driver', 'web');
        $app['config']->set('activitylog.enabled', false);
    }

    #[Override]
    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BadgeableColumnServiceProvider::class,
            SupportServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            TablesServiceProvider::class,
            ActionsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentJalaliServiceProvider::class,
            PermissionServiceProvider::class,
            MultitenancyServiceProvider::class,
            FilamentServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }
}
