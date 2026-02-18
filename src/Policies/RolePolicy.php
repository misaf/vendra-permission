<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Misaf\VendraPermission\Enums\RolePolicyEnum;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraUser\Models\User;

final class RolePolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->can(RolePolicyEnum::CREATE);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::DELETE);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(RolePolicyEnum::DELETE_ANY);
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::FORCE_DELETE);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can(RolePolicyEnum::FORCE_DELETE_ANY);
    }

    public function replicate(User $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::REPLICATE);
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::RESTORE);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(RolePolicyEnum::RESTORE_ANY);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::UPDATE);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can(RolePolicyEnum::VIEW);
    }

    public function viewAny(User $user): bool
    {
        return $user->can(RolePolicyEnum::VIEW_ANY);
    }
}
