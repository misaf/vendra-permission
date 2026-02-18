<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraTenant\Models\Tenant;

/**
 * @extends Factory<Permission>
 */
final class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'tenant_id'   => Tenant::factory(),
            'name'        => fake()->sentences(1, true),
            'description' => fake()->realTextBetween(100, 200),
            'guard_name'  => fake()->randomElement('web', 'sanctum'),
        ];
    }

    public function forTenant(Tenant|int $tenant): static
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->state(fn(): array => ['tenant_id' => $tenantId]);
    }
}
