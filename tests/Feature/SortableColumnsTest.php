<?php

declare(strict_types=1);

use Awcodes\BadgeableColumn\Components\BadgeableColumn;
use Misaf\VendraPermission\Database\Factories\PermissionFactory;
use Misaf\VendraPermission\Database\Factories\RoleFactory;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ListPermissions;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\ListRoles;
use Misaf\VendraPermission\Tests\Support\PermissionModuleTestContext;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    PermissionModuleTestContext::setUpFilamentAdminContext();
});

it('sorts the permissions table by every sortable column following the stored values', function (): void {
    $first = PermissionFactory::new()->forGuard('web')->createOne(['name' => 'aaa permission']);
    $second = PermissionFactory::new()->forGuard('web')->createOne(['name' => 'bbb permission']);

    $component = livewire(ListPermissions::class)->call('loadTable');

    expect($component)
        ->toSortByEverySortableColumn([$first, $second])
        ->and($component->instance()->getTable()->getDefaultGroup())->toBeNull();
});

it('sorts the roles table by every sortable column following the stored values', function (): void {
    $first = RoleFactory::new()->forGuard('web')->createOne(['name' => 'aaa role']);
    $second = RoleFactory::new()->forGuard('web')->createOne(['name' => 'bbb role']);

    $component = livewire(ListRoles::class)->call('loadTable');

    expect($component)
        ->toSortByEverySortableColumn([$first, $second])
        ->and($component->instance()->getTable()->getDefaultGroup())->toBeNull();
});

it('renders the permission count as a role suffix badge', function (): void {
    $role = RoleFactory::new()->forGuard('web')->createOne();
    $permissions = PermissionFactory::new()->forGuard('web')->count(2)->create();
    $role->permissions()->sync($permissions);

    livewire(ListRoles::class)
        ->call('loadTable')
        ->assertTableColumnExists(
            'name',
            function (BadgeableColumn $column): bool {
                $formattedState = (string) $column->formatState('Test role');

                return str_contains($formattedState, 'badgeable-column-badge')
                    && str_contains($formattedState, '2');
            },
            $role,
        );
});
