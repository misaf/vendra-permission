<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Misaf\VendraActivityLog\Concerns\HasDefaultActivityLogOptions;
use Misaf\VendraPermission\Database\Factories\RoleFactory;
use Misaf\VendraTenant\Traits\BelongsToTenant;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $name
 * @property string|null $description
 * @property string $guard_name
 */
#[Fillable(['name', 'description', 'guard_name'])]
#[Hidden(['tenant_id'])]
#[UseFactory(RoleFactory::class)]
final class Role extends SpatieRole
{
    use BelongsToTenant;
    use HasDefaultActivityLogOptions;

    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    use LogsActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'tenant_id'   => 'integer',
            'name'        => 'string',
            'description' => 'string',
            'guard_name'  => 'string',
        ];
    }

}
