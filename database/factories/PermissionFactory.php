<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraTenant\Models\Tenant;
use RuntimeException;

/**
 * @extends Factory<Permission>
 */
final class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $guardNames = $this->configuredGuardNames();

        return [
            'tenant_id'   => Tenant::factory(),
            'name'        => fake()->sentences(1, true),
            'description' => fake()->realTextBetween(100, 200),
            'guard_name'  => Arr::random($guardNames),
        ];
    }

    public function forTenant(Tenant|int $tenant): static
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->state(fn(): array => ['tenant_id' => $tenantId]);
    }

    public function forGuard(string $guardName): static
    {
        $configuredGuardNames = $this->configuredGuardNames();

        if ( ! in_array($guardName, $configuredGuardNames, true)) {
            throw new RuntimeException("The guard [{$guardName}] is not configured in auth.guards.");
        }

        return $this->state(fn(): array => ['guard_name' => $guardName]);
    }

    /**
     * @return list<string>
     */
    private function configuredGuardNames(): array
    {
        $guardNames = array_keys(Config::array('auth.guards'));

        if ([] === $guardNames) {
            throw new RuntimeException('No guards are configured in auth.guards.');
        }

        return $guardNames;
    }
}
