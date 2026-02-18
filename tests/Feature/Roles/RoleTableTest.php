<?php

declare(strict_types=1);

use Awcodes\BadgeableColumn\Components\BadgeableColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\ListRoles;
use Misaf\VendraPermission\Models\Role;
use Misaf\VendraTenant\Models\Tenant;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    setUpFilamentAdminContextForPermissionModule();
});

function createRoleForTableTest(array $attributes = []): Role
{
    $tenant = Tenant::current();

    if ( ! $tenant) {
        throw new RuntimeException('No current tenant is set for role table tests.');
    }

    $guardName = Arr::pull($attributes, 'guard_name');

    $factory = Role::factory()
        ->forTenant($tenant);

    if (is_string($guardName)) {
        $factory = $factory->forGuard($guardName);
    }

    return $factory->create($attributes);
}

it('lists tenant roles in the roles table', function (): void {
    $initialRolesCount = Role::query()->count();

    $firstRole = createRoleForTableTest();
    $secondRole = createRoleForTableTest();

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertCanSeeTableRecords([$firstRole, $secondRole])
        ->assertCountTableRecords($initialRolesCount + 2);
});

it('filters roles by global table search using name', function (): void {
    $operatorRole = createRoleForTableTest(['name' => 'operator']);
    $auditorRole = createRoleForTableTest(['name' => 'auditor']);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertCanSeeTableRecords([$operatorRole, $auditorRole])
        ->searchTable($operatorRole->name)
        ->assertCanSeeTableRecords([$operatorRole])
        ->assertCanNotSeeTableRecords([$auditorRole]);
});

it('restores full result set when table search is cleared', function (): void {
    $managerRole = createRoleForTableTest(['name' => 'manager']);
    $reviewerRole = createRoleForTableTest(['name' => 'reviewer']);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->searchTable($managerRole->name)
        ->assertCanSeeTableRecords([$managerRole])
        ->assertCanNotSeeTableRecords([$reviewerRole])
        ->searchTable(null)
        ->assertCanSeeTableRecords([$managerRole, $reviewerRole]);
});

it('shows the query builder filter', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableFilterExists('queryBuilder')
        ->assertTableFilterVisible('queryBuilder');
});

it('can reset and remove query builder filters', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->filterTable('queryBuilder', ['rules' => []])
        ->resetTableFilters()
        ->filterTable('queryBuilder', ['rules' => []])
        ->removeTableFilters();
});

it('renders key table columns', function (string $column): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertCanRenderTableColumn($column)
        ->assertTableColumnExists($column);
})->with([
    'row',
    'name',
    'guard_name',
]);

it('includes default metadata columns', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnExists('created_at')
        ->assertTableColumnExists('updated_at');
});

it('provides expected guard options in the select column', function (): void {
    $role = createRoleForTableTest(['name' => 'select-column-role']);

    $guardNames = array_keys(Config::array('auth.guards'));

    $guardOptions = Arr::mapWithKeys(
        $guardNames,
        fn(string $guard): array => [$guard => $guard],
    );

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableSelectColumnHasOptions('guard_name', $guardOptions, $role)
        ->assertTableSelectColumnDoesNotHaveOptions('guard_name', ['__invalid__' => '__invalid__'], $role);
});

it('marks timestamp columns as toggleable and hidden by default', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnExists('created_at', fn(TextColumn $column): bool => $column->isToggleable() && $column->isToggledHiddenByDefault())
        ->assertTableColumnExists('updated_at', fn(TextColumn $column): bool => $column->isToggleable() && $column->isToggledHiddenByDefault())
        ->assertCanNotRenderTableColumn('created_at')
        ->assertCanNotRenderTableColumn('updated_at');
});

it('can show hidden timestamp columns after toggling all columns on', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->toggleAllTableColumns(true)
        ->assertCanRenderTableColumn('created_at')
        ->assertCanRenderTableColumn('updated_at');
});

it('keeps hidden timestamp columns hidden when toggling all columns off', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->toggleAllTableColumns(false)
        ->assertCanNotRenderTableColumn('created_at')
        ->assertCanNotRenderTableColumn('updated_at');
});

it('renders role description below the name column', function (): void {
    $role = createRoleForTableTest([
        'name'        => 'editor',
        'description' => 'example description to demonstrate below name',
    ]);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnHasDescription('name', $role->description, $role, 'below')
        ->assertTableColumnExists('name', function (BadgeableColumn $column) use ($role): bool {
            return $column->getDescriptionBelow() === $role->description;
        })
        ->assertTableColumnDoesNotHaveDescription('name', $role->description, $role, 'above');
});

it('does not render a description when role description is empty', function (): void {
    $role = createRoleForTableTest([
        'name'        => 'no-description-role',
        'description' => null,
    ]);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnDoesNotHaveDescription('name', 'any-description', $role, 'below');
});

it('asserts guard_name state for a record', function (): void {
    $role = createRoleForTableTest(['name' => 'state-check-role']);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnStateSet('guard_name', $role->guard_name, $role);
});

it('renders created_at and updated_at with the expected table format', function (): void {
    $role = createRoleForTableTest(['name' => 'formatted-date-role']);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->toggleAllTableColumns(true)
        ->assertTableColumnFormattedStateSet('created_at', $role->created_at->format('Y-m-d H:i'), $role)
        ->assertTableColumnFormattedStateSet('updated_at', $role->updated_at->format('Y-m-d H:i'), $role);
});

it('applies ltr cell attributes to timestamp columns', function (): void {
    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnExists('created_at', fn(TextColumn $column): bool => $column->getExtraCellAttributes() === ['dir' => 'ltr'])
        ->assertTableColumnExists('updated_at', fn(TextColumn $column): bool => $column->getExtraCellAttributes() === ['dir' => 'ltr']);
});

it('can assert extra attributes on the name column', function (): void {
    $role = createRoleForTableTest(['name' => 'extra-attributes-role']);

    livewire(ListRoles::class)
        ->assertOk()
        ->loadTable()
        ->assertTableColumnHasExtraAttributes('name', [], $role)
        ->assertTableColumnDoesNotHaveExtraAttributes('name', ['class' => 'example'], $role);
});
