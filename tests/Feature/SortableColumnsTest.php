<?php

declare(strict_types=1);

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

    expect(livewire(ListPermissions::class)->call('loadTable'))
        ->toSortByEverySortableColumn([$first, $second]);
});

it('sorts the roles table by every sortable column following the stored values', function (): void {
    $first = RoleFactory::new()->forGuard('web')->createOne(['name' => 'aaa role']);
    $second = RoleFactory::new()->forGuard('web')->createOne(['name' => 'bbb role']);

    expect(livewire(ListRoles::class)->call('loadTable'))
        ->toSortByEverySortableColumn([$first, $second]);
});
