<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Misaf\VendraPermission\Enums\PermissionPolicyEnum;
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraUser\Models\User;

final class PermissionPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->can(PermissionPolicyEnum::CREATE);
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::DELETE);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(PermissionPolicyEnum::DELETE_ANY);
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::FORCE_DELETE);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can(PermissionPolicyEnum::FORCE_DELETE_ANY);
    }

    public function replicate(User $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::REPLICATE);
    }

    public function restore(User $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::RESTORE);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(PermissionPolicyEnum::RESTORE_ANY);
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::UPDATE);
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->can(PermissionPolicyEnum::VIEW);
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionPolicyEnum::VIEW_ANY);
    }
}
