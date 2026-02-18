<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Enums;

enum PermissionFeatureEnum: string
{
    case MODULE_ENABLED = 'vendra-permission.module-enabled';
    case ROLE_MANAGEMENT = 'vendra-permission.role-management';
    case PERMISSION_MANAGEMENT = 'vendra-permission.permission-management';
    case BULK_ROLE_ASSIGNMENT = 'vendra-permission.bulk-role-assignment';
}
