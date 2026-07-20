<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Misaf\VendraPermission\Actions\CreatePermissionAction;
use Misaf\VendraPermission\Actions\CreateRoleAction;

it('creates the role in the current tenant with the default guard when no tenant is supplied', function (): void {
    $current = createTestTenant();
    switchToTestTenant($current);

    $role = new CreateRoleAction()->execute(
        tenant: null,
        name: 'editor',
        description: 'Edits content',
    );

    expect($role->name)->toBe('editor')
        ->and($role->description)->toBe('Edits content')
        ->and($role->guard_name)->toBe(Config::string('auth.defaults.guard'))
        ->and($role->tenant_id)->toBe($current->id);
});

it('creates the role inside the supplied tenant context', function (): void {
    $tenant = createTestTenant();

    $role = new CreateRoleAction()->execute(
        tenant: $tenant,
        name: 'editor',
        guardName: 'web',
    );

    expect($role->tenant_id)->toBe($tenant->id)
        ->and($role->guard_name)->toBe('web')
        ->and(currentTestTenant())->toBeNull();
});

it('creates the permission in the current tenant when no tenant is supplied', function (): void {
    $current = createTestTenant();
    switchToTestTenant($current);

    $permission = new CreatePermissionAction()->execute(
        tenant: null,
        name: 'view-any-report',
        description: null,
        guardName: 'web',
    );

    expect($permission->name)->toBe('view-any-report')
        ->and($permission->guard_name)->toBe('web')
        ->and($permission->tenant_id)->toBe($current->id);
});

it('creates the permission inside the supplied tenant context', function (): void {
    $tenant = createTestTenant();

    $permission = new CreatePermissionAction()->execute(
        tenant: $tenant,
        name: 'view-any-report',
        description: 'Read access to reports',
        guardName: 'web',
    );

    expect($permission->tenant_id)->toBe($tenant->id)
        ->and($permission->description)->toBe('Read access to reports')
        ->and(currentTestTenant())->toBeNull();
});
