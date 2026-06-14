<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Actions;

use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;

final class CreateRoleAction
{
    public function execute(
        Tenant $tenant,
        string $name,
        string $guardName,
    ): Role {
        /** @var Role $role */
        $role = $tenant->execute(static fn() => Role::findOrCreate($name, $guardName));

        return $role;
    }
}
