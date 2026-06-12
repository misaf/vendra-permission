<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Actions;

use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;
use Spatie\Permission\PermissionRegistrar;

final class CreateRoleAction
{
    public function __construct(private readonly PermissionRegistrar $permissionRegistrar) {}

    public function execute(
        Tenant $tenant,
        string $name,
        ?string $description,
        string $guardName,
    ): Role {
        $role = $tenant->roles()->firstOrCreate(
            [
                'name'       => $name,
                'guard_name' => $guardName,
            ],
            [
                'description' => $description,
            ],
        );

        $this->permissionRegistrar->forgetCachedPermissions();

        return $role;
    }
}
