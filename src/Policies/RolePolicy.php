<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Policies;

use Misaf\VendraPermission\Enums\RolePolicyEnum;
use Misaf\VendraSupport\Concerns\AuthorizesCreateAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesDeleteAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesForceDeleteAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesRestoreAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesSandboxMode;
use Misaf\VendraSupport\Concerns\AuthorizesUpdateAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesViewAbilities;
use Misaf\VendraSupport\Concerns\ResolvesPolicyPermissions;

final class RolePolicy
{
    use AuthorizesCreateAbilities;
    use AuthorizesDeleteAbilities;
    use AuthorizesForceDeleteAbilities;
    use AuthorizesRestoreAbilities;
    use AuthorizesSandboxMode;
    use AuthorizesUpdateAbilities;
    use AuthorizesViewAbilities;
    use ResolvesPolicyPermissions;

    protected static function permissionEnum(): string
    {
        return RolePolicyEnum::class;
    }
}
