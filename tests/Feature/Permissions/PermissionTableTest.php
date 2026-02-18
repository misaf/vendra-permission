<?php

declare(strict_types=1);

use Filament\Tables\Columns\TextColumn;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ListPermissions;
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;

use function Pest\Livewire\livewire;

$tenant = null;

beforeEach(function () use (&$tenant): void {
    $tenant = setUpFilamentAdminContextForPermissionModule();
});

describe('table rendering', function () use (&$tenant): void {
    it('renders table', function (): void {
        livewire(ListPermissions::class)
            ->loadTable()
            ->assertSuccessful();
    });

    it('lists records', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permissions = Permission::factory()
            ->count(2)
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertCanSeeTableRecords($permissions);
    });

    it('counts records', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permissions = Permission::factory()
            ->count(2)
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertCountTableRecords($permissions->count());
    });

    it('paginates records', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $firstGroupPermissions = Permission::factory()
            ->count(10)
            ->forTenant($tenant)
            ->create();

        $otherGroupPermissions = Permission::factory()
            ->count(10)
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertCanSeeTableRecords($firstGroupPermissions)
            ->assertCanNotSeeTableRecords($otherGroupPermissions)
            ->call('gotoPage', 2)
            ->assertCanSeeTableRecords($otherGroupPermissions)
            ->assertCanNotSeeTableRecords($firstGroupPermissions);
    });

    it('shows empty table when no records exist', function (): void {
        livewire(ListPermissions::class)
            ->loadTable()
            ->assertCountTableRecords(0);
    });
});

describe('table columns', function () use (&$tenant): void {
    it('renders columns', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        Permission::factory()
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertCanRenderTableColumn($column);
    })->with([
        'row column'     => 'row',
        'roles relation' => 'roles.name',
        'name column'    => 'name'
    ]);

    it('hides columns by default', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        Permission::factory()
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertCanNotRenderTableColumn($column);
    })->with([
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);
});

describe('table search', function () use (&$tenant): void {
    it('searches records', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $firstPermission = Permission::factory()
            ->forTenant($tenant)
            ->create([
                'name' => 'alpha-permission-search',
            ]);

        $otherPermissions = Permission::factory()
            ->forTenant($tenant)
            ->create([
                'name' => 'beta-permission-search',
            ]);

        livewire(ListPermissions::class)
            ->loadTable()
            ->searchTable($firstPermission->name)
            ->assertCanSeeTableRecords([$firstPermission])
            ->assertCanNotSeeTableRecords([$otherPermissions]);
    });

    it('shows no records for unmatched search', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->searchTable('permission-name-that-does-not-exist')
            ->assertCanNotSeeTableRecords([$permission])
            ->assertCountTableRecords(0);
    });
});

describe('table sorting', function () use (&$tenant): void {
    it('sorts records', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $sortPrefix = uniqid('permission-sort-', true);

        $permissions = Permission::factory()
            ->count(3)
            ->forTenant($tenant)
            ->sequence(
                ['name' => "{$sortPrefix}-c"],
                ['name' => "{$sortPrefix}-a"],
                ['name' => "{$sortPrefix}-b"],
            )
            ->create();

        $sortedPermissionsAsc = Permission::query()
            ->whereKey($permissions->pluck('id'))
            ->orderBy('name', 'asc')
            ->get();

        $sortedPermissionsDesc = Permission::query()
            ->whereKey($permissions->pluck('id'))
            ->orderBy('name', 'desc')
            ->get();

        livewire(ListPermissions::class)
            ->loadTable()
            ->sortTable('name', 'asc')
            ->assertCanSeeTableRecords($sortedPermissionsAsc, inOrder: true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords($sortedPermissionsDesc, inOrder: true);
    });
});

describe('column states', function () use (&$tenant): void {
    it('sets row index state', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnStateSet('row', 1, $permission);
    });

    it('sets roles relation state', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $role = Role::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard($role->guard_name)
            ->create();

        $role->givePermissionTo($permission);

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnStateSet('roles.name', $permission->roles()->pluck('name'), $permission);
    });

    it('sets raw column state', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnStateSet($column, $permission->{$column}, $permission);
    })->with([
        'name column'       => 'name',
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);

    it('formats row index state', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnFormattedStateSet('row', 1, $permission);
    });

    it('formats roles relation state', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $role = Role::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard($role->guard_name)
            ->create();

        $role->givePermissionTo($permission);

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnFormattedStateSet('roles.name', $permission->roles()->pluck('name'), $permission);
    });

    it('formats name column state', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnFormattedStateSet('name', $permission->name, $permission);
    });

    it('formats timestamp column state', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnFormattedStateSet($column, $permission->{$column}->format('Y-m-d H:i'), $permission);
    })->with([
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);
});

describe('column existence', function () use (&$tenant): void {
    it('resolves name description below', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnExists('name', function (TextColumn $column) use ($permission): bool {
                return $column->getDescriptionBelow() === $permission->description;
            }, $permission);
    });

    it('has configured columns', function (string $column): void {
        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnExists($column);
    })->with([
        'row column'        => 'row',
        'roles relation'    => 'roles.name',
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);
});

describe('column visibility', function (): void {
    it('shows configured visible columns', function (string $column): void {
        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnVisible($column);
    })->with([
        'row column'        => 'row',
        'roles relation'    => 'roles.name',
        'name column'       => 'name',
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);
});

describe('column descriptions', function () use (&$tenant): void {
    it('shows name description below', function () use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnHasDescription('name', $permission->description, $permission, 'below');
    });

    it('hides description below for non-description columns', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnDoesNotHaveDescription($column, $permission->description, $permission, 'below');
    })->with([
        'row column'        => 'row',
        'roles relation'    => 'roles.name',
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);

    it('hides description above for all configured columns', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        $permission = Permission::factory()
            ->forTenant($tenant)
            ->forGuard('web')
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->assertTableColumnDoesNotHaveDescription($column, $permission->description, $permission, 'above');
    })->with([
        'row column'        => 'row',
        'roles relation'    => 'roles.name',
        'name column'       => 'name',
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);
});

describe('toggleable columns', function () use (&$tenant): void {
    it('shows hidden columns after toggle', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        Permission::factory()
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->toggleAllTableColumns()
            ->assertCanRenderTableColumn($column);
    })->with([
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);

    it('keeps hidden columns hidden after toggle off', function (string $column) use (&$tenant): void {
        assert($tenant instanceof Tenant);

        Permission::factory()
            ->forTenant($tenant)
            ->create();

        livewire(ListPermissions::class)
            ->loadTable()
            ->toggleAllTableColumns(false)
            ->assertCanNotRenderTableColumn($column);
    })->with([
        'created at column' => 'created_at',
        'updated at column' => 'updated_at'
    ]);
});
