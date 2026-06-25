<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Actions;

use Illuminate\Support\Facades\Config;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;

final class CreateRoleAction
{
    public function execute(
        Tenant $tenant,
        string $name,
        ?string $description = null,
        ?string $guardName = null,
    ): Role {
        $guardName = blank($guardName)
            ? Config::string('auth.defaults.guard')
            : $guardName;

        /** @var Role $role */
        $role = $tenant->execute(static fn() => Role::create([
            'name'        => $name,
            'description' => $description,
            'guard_name'  => $guardName,
        ]));

        return $role;
    }
}
