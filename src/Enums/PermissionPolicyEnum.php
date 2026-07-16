<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Enums;

enum PermissionPolicyEnum: string
{
    case Create = 'create-permission';
    case Delete = 'delete-permission';
    case DeleteAny = 'delete-any-permission';
    case ForceDelete = 'force-delete-permission';
    case ForceDeleteAny = 'force-delete-any-permission';
    case Replicate = 'replicate-permission';
    case Restore = 'restore-permission';
    case RestoreAny = 'restore-any-permission';
    case Update = 'update-permission';
    case View = 'view-permission';
    case ViewAny = 'view-any-permission';
}
