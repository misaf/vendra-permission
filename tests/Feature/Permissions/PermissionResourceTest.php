<?php

declare(strict_types=1);

use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\CreatePermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\EditPermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ListPermissions;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ViewPermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\PermissionResource;

it('allows permission resource access when module and permission management features are active', function (): void {
    createCurrentTenantForPermissionModule([
        PermissionFeatureEnum::MODULE_ENABLED->value,
        PermissionFeatureEnum::PERMISSION_MANAGEMENT->value,
    ]);

    expect(PermissionResource::canAccess())->toBeTrue();
});

it('denies permission resource access when permission management feature is inactive', function (): void {
    createCurrentTenantForPermissionModule([
        PermissionFeatureEnum::MODULE_ENABLED->value,
    ]);

    expect(PermissionResource::canAccess())->toBeFalse();
});

it('wires permission resource pages correctly', function (): void {
    $pages = PermissionResource::getPages();

    expect($pages)->toHaveKeys([
        'index',
        'create',
        'view',
        'edit',
    ]);

    expect($pages['index']->getPage())->toBe(ListPermissions::class);
    expect($pages['create']->getPage())->toBe(CreatePermission::class);
    expect($pages['view']->getPage())->toBe(ViewPermission::class);
    expect($pages['edit']->getPage())->toBe(EditPermission::class);
});
