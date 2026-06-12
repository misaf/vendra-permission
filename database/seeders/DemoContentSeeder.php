<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Misaf\VendraPermission\Actions\CreateRoleAction;
use Misaf\VendraTenant\Models\Tenant;

final class DemoContentSeeder extends Seeder
{
    public function __construct(private readonly CreateRoleAction $createRoleAction) {}

    public function run(): void
    {
        $tenant = Tenant::current();

        if ( ! $tenant) {
            $this->command->error('Tenants not found. Please run TenantSeeder first.');
            return;
        }

        $this->seedDefaultRole($tenant);
    }

    private function seedDefaultRole(Tenant $tenant): void
    {
        $roleName = Config::string('vendra-permission.super_admin_role', 'super-admin');
        $guardName = Config::string('auth.defaults.guard', 'web');

        $role = $this->createRoleAction->execute($tenant, $roleName, null, $guardName);

        $message = $role->wasRecentlyCreated ? 'Created' : 'Found existing';

        $this->command->info("{$message} default role [{$roleName}] for {$tenant->slug} tenant.");
    }
}
