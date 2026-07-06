<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Misaf\VendraPermission\Enums\RolePolicyEnum;
use Misaf\VendraPermission\Models\Role;

final class RolePolicy
{
    use HandlesAuthorization;

    public function create(Authorizable $user): bool
    {
        return $user->can(RolePolicyEnum::CREATE->value);
    }

    public function delete(Authorizable $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::DELETE->value);
    }

    public function deleteAny(Authorizable $user): bool
    {
        return $user->can(RolePolicyEnum::DELETE_ANY->value);
    }

    public function forceDelete(Authorizable $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::FORCE_DELETE->value);
    }

    public function forceDeleteAny(Authorizable $user): bool
    {
        return $user->can(RolePolicyEnum::FORCE_DELETE_ANY->value);
    }

    public function replicate(Authorizable $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::REPLICATE->value);
    }

    public function restore(Authorizable $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::RESTORE->value);
    }

    public function restoreAny(Authorizable $user): bool
    {
        return $user->can(RolePolicyEnum::RESTORE_ANY->value);
    }

    public function update(Authorizable $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::UPDATE->value);
    }

    public function view(Authorizable $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::VIEW->value);
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can(RolePolicyEnum::VIEW_ANY->value);
    }
}
