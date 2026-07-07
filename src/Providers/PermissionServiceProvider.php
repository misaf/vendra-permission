<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Providers;

use Filament\Panel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Laravel\Pennant\Feature;
use Misaf\VendraPermission\Console\Commands\FeatureToggleCommand;
use Misaf\VendraPermission\Console\Commands\SeedCommand;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraPermission\PermissionPlugin;
use Misaf\VendraSupport\Contracts\TenantResolver;
use Misaf\VendraSupport\Support\TenantAwareness;
use Misaf\VendraSupport\Support\TenantSeeders;
use Misaf\VendraUser\Models\User;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class PermissionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('vendra-permission')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'add_tenant_id_column_to_roles_table',
                'add_tenant_id_column_to_permissions_table',
                'add_description_column_to_roles_table',
                'add_description_column_to_permissions_table',
            ])
            ->hasCommands(
                FeatureToggleCommand::class,
                SeedCommand::class,
            )
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->askToStarRepoOnGitHub('misaf/vendra-permission');
            });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            if ('admin' !== $panel->getId()) {
                return;
            }

            $panel->plugin(PermissionPlugin::make());
        });
    }

    public function packageBooted(): void
    {
        $this->app->make(TenantSeeders::class)->register('vendra-permission:seed', priority: 10);

        AboutCommand::add('Vendra Permission', fn() => ['Version' => 'dev-master']);

        Gate::after(function (User $user): ?true {
            return $user->hasRole(Config::string('vendra-permission.super_admin_role', 'superadmin')) ? true : null;
        });

        $this->discoverPackageFeatures();
        $this->registerTenantFeatures();
    }

    private function discoverPackageFeatures(): void
    {
        $featureNamespace = 'Misaf\\VendraPermission\\Features';
        $featurePath = __DIR__ . '/Features';

        if (Config::boolean('vendra-permission.features.discover', false) && is_dir($featurePath)) {
            Feature::discover($featureNamespace, $featurePath);
        }
    }

    private function registerTenantFeatures(): void
    {
        foreach (PermissionFeatureEnum::cases() as $feature) {
            Feature::define($feature->value, function (mixed $scope) use ($feature): bool {
                if ( ! Config::boolean('vendra-permission.features.enabled', true)) {
                    return false;
                }

                if (TenantAwareness::enabled()) {
                    $tenantModel = app(TenantResolver::class)->modelClass();

                    if ( ! $scope instanceof $tenantModel) {
                        return false;
                    }
                }

                $defaults = Config::array('vendra-permission.features.defaults');

                return (bool) ($defaults[$feature->value] ?? false);
            });
        }
    }
}
