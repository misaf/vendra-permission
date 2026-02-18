<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Schemas\Components;

use Filament\Forms\Components\Select;
use Illuminate\Validation\Rule;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;

final class RolesSelect
{
    public static function make(string $name = 'roles'): Select
    {
        return Select::make($name)
            ->columnSpanFull()
            ->label(__('vendra-permission::navigation.role'))
            ->multiple()
            ->native(false)
            ->preload()
            ->rule('array')
            ->nestedRecursiveRules([
                'integer',
                Rule::exists((new Role())->getTable(), 'id')
                    ->where('tenant_id', Tenant::current()?->id),
            ])
            ->searchable();
    }
}
