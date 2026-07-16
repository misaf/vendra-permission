<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Enums;

enum RolePolicyEnum: string
{
    case Create = 'create-role';
    case Delete = 'delete-role';
    case DeleteAny = 'delete-any-role';
    case ForceDelete = 'force-delete-role';
    case ForceDeleteAny = 'force-delete-any-role';
    case Replicate = 'replicate-role';
    case Restore = 'restore-role';
    case RestoreAny = 'restore-any-role';
    case Update = 'update-role';
    case View = 'view-role';
    case ViewAny = 'view-any-role';
}
