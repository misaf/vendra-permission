<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Misaf\VendraPermission\Enums\PermissionPolicyEnum;
use Misaf\VendraPermission\Models\Permission;

final class PermissionPolicy
{
    use HandlesAuthorization;

    public function create(Authorizable $user): bool
    {
        return $user->can(PermissionPolicyEnum::CREATE->value);
    }

    public function delete(Authorizable $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::DELETE->value);
    }

    public function deleteAny(Authorizable $user): bool
    {
        return $user->can(PermissionPolicyEnum::DELETE_ANY->value);
    }

    public function forceDelete(Authorizable $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::FORCE_DELETE->value);
    }

    public function forceDeleteAny(Authorizable $user): bool
    {
        return $user->can(PermissionPolicyEnum::FORCE_DELETE_ANY->value);
    }

    public function replicate(Authorizable $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::REPLICATE->value);
    }

    public function restore(Authorizable $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::RESTORE->value);
    }

    public function restoreAny(Authorizable $user): bool
    {
        return $user->can(PermissionPolicyEnum::RESTORE_ANY->value);
    }

    public function update(Authorizable $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::UPDATE->value);
    }

    public function view(Authorizable $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::VIEW->value);
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can(PermissionPolicyEnum::VIEW_ANY->value);
    }
}
