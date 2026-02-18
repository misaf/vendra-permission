<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Enums;

enum PermissionPolicyEnum: string
{
    case CREATE = 'create-permission';
    case DELETE = 'delete-permission';
    case DELETE_ANY = 'delete-any-permission';
    case FORCE_DELETE = 'force-delete-permission';
    case FORCE_DELETE_ANY = 'force-delete-any-permission';
    case REPLICATE = 'replicate-permission';
    case RESTORE = 'restore-permission';
    case RESTORE_ANY = 'restore-any-permission';
    case UPDATE = 'update-permission';
    case VIEW = 'view-permission';
    case VIEW_ANY = 'view-any-permission';
}
