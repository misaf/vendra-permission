<?php

declare(strict_types=1);

namespace Misaf\VendraPermission;

use Filament\Panel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
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
                'add_description_column_to_permissions_table'
            ])
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

            $panel->strictAuthorization();
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Vendra Permission', fn() => ['Version' => 'dev-master']);

        $superAdminRole = Config::string('vendra-permission.super_admin_role');

        Gate::after(function (User $user) use ($superAdminRole): ?true {
            return $user->hasRole($superAdminRole) ? true : null;
        });
    }
}
