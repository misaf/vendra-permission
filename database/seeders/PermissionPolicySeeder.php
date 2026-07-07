<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Seeders;

use Misaf\VendraPermission\Enums\PermissionPolicyEnum;
use Misaf\VendraPermission\Enums\RolePolicyEnum;
use Misaf\VendraPermission\PermissionPlugin;
use Misaf\VendraSupport\Database\Seeders\PermissionPolicySeeder as BasePermissionPolicySeeder;

final class PermissionPolicySeeder extends BasePermissionPolicySeeder
{
    protected const string MODULE_NAME = PermissionPlugin::ID;

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
