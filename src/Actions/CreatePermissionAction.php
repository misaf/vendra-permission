<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Actions;

use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraTenant\Models\Tenant;

final class CreatePermissionAction
{
    public function execute(
        Tenant $tenant,
        string $name,
        ?string $description,
        string $guardName,
    ): Permission {
        /** @var Permission $permission */
        $permission = $tenant->execute(static fn() => Permission::create([
            'name'        => $name,
            'description' => $description,
            'guard_name'  => $guardName,
        ]));

        return $permission;
    }
}
