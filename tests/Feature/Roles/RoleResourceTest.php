<?php

declare(strict_types=1);

use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\RelationManagers\PermissionRelationManager;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\CreateRole;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\EditRole;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\ListRoles;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\ViewRole;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\RoleResource;

it('allows role resource access when module and role management features are active', function (): void {
    createCurrentTenantForPermissionModule([
        PermissionFeatureEnum::MODULE_ENABLED->value,
        PermissionFeatureEnum::ROLE_MANAGEMENT->value,
    ]);

    expect(RoleResource::canAccess())->toBeTrue();
});

it('denies role resource access when role management feature is inactive', function (): void {
    createCurrentTenantForPermissionModule([
        PermissionFeatureEnum::MODULE_ENABLED->value,
    ]);

    expect(RoleResource::canAccess())->toBeFalse();
});

it('wires role resource pages and relations correctly', function (): void {
    $pages = RoleResource::getPages();

    expect($pages)->toHaveKeys([
        'index',
        'create',
        'view',
        'edit',
    ]);

    expect($pages['index']->getPage())->toBe(ListRoles::class);
    expect($pages['create']->getPage())->toBe(CreateRole::class);
    expect($pages['view']->getPage())->toBe(ViewRole::class);
    expect($pages['edit']->getPage())->toBe(EditRole::class);

    expect(RoleResource::getRelations())->toBe([
        PermissionRelationManager::class,
    ]);
});
