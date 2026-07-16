<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Enums;

enum PermissionFeatureEnum: string
{
    case ModuleEnabled = 'vendra-permission.module-enabled';
    case RoleManagement = 'vendra-permission.role-management';
    case PermissionManagement = 'vendra-permission.permission-management';
    case BulkRoleAssignment = 'vendra-permission.bulk-role-assignment';
}
