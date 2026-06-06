<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Misaf\VendraTenant\Models\Tenant;
use Spatie\Permission\PermissionRegistrar;

final class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::current();

        if ( ! $tenant) {
            $this->command?->error('Tenants not found. Please run TenantSeeder first.');
            return;
        }

        $this->seedDefaultRole($tenant);
    }

    private function seedDefaultRole(Tenant $tenant): void
    {
        $roleModel = Config::string('permission.models.role');
        $roleName = Config::string('vendra-permission.super_admin_role', 'super-admin');
        $guardName = Config::string('auth.defaults.guard', 'web');

        $attributes = [
            'name'       => $roleName,
            'guard_name' => $guardName,
        ];

        $values = Schema::hasColumn('roles', 'tenant_id')
            ? ['tenant_id' => $tenant->id]
            : [];

        $role = $roleModel::query()->firstOrCreate($attributes, $values);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $message = $role->wasRecentlyCreated ? 'Created' : 'Found existing';

        $this->command?->info("{$message} default role [{$roleName}] for {$tenant->slug} tenant.");
    }
}
