<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Seeders;

use Misaf\VendraPermission\Enums\PermissionPolicyEnum;
use Misaf\VendraPermission\Enums\RolePolicyEnum;
use Misaf\VendraPermission\PermissionPlugin;
use Misaf\VendraSupport\Concerns\RequiresCurrentTenant;
use Misaf\VendraSupport\Database\Seeders\PermissionPolicySeeder as BasePermissionPolicySeeder;

final class PermissionPolicySeeder extends BasePermissionPolicySeeder
{
    use RequiresCurrentTenant;

    protected const string MODULE_NAME = PermissionPlugin::ID;

    public function run(): void
    {
        $tenant = $this->currentTenant();

        $this->seedPermissionPolicies($tenant->getKey());
    }

    /**
     * @return list<string>
     */
    protected function policies(): array
    {
        return [
            ...array_column(RolePolicyEnum::cases(), 'value'),
            ...array_column(PermissionPolicyEnum::cases(), 'value'),
        ];
    }
}
