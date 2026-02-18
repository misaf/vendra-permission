<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Enums;

enum RolePolicyEnum: string
{
    case CREATE = 'create-role';
    case DELETE = 'delete-role';
    case DELETE_ANY = 'delete-any-role';
    case FORCE_DELETE = 'force-delete-role';
    case FORCE_DELETE_ANY = 'force-delete-any-role';
    case REPLICATE = 'replicate-role';
    case RESTORE = 'restore-role';
    case RESTORE_ANY = 'restore-any-role';
    case UPDATE = 'update-role';
    case VIEW = 'view-role';
    case VIEW_ANY = 'view-any-role';
}
