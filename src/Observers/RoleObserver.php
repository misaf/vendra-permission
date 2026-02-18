<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Observers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Misaf\VendraPermission\Models\Role;

final class RoleObserver implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public function deleted(Role $role): void
    {
        $role->permissions()->delete();
    }
}
