<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Misaf\VendraPermission\Database\Factories\PermissionFactory;
use Misaf\VendraTenant\Traits\BelongsToTenant;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string|null $description
 */
final class Permission extends SpatiePermission
{
    use BelongsToTenant;

    /** @use HasFactory<PermissionFactory> */
    use HasFactory;

    use LogsActivity;

    protected $casts = [
        'id'          => 'integer',
        'tenant_id'   => 'integer',
        'name'        => 'string',
        'description' => 'string',
        'guard_name'  => 'string',
    ];

    protected $fillable = [
        'name',
        'description',
        'guard_name',
    ];

    protected $hidden = [
        'tenant_id',
    ];

    protected static function newFactory()
    {
        return PermissionFactory::new();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logExcept(['id']);
    }
}
