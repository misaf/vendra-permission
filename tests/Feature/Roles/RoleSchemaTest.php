<?php

declare(strict_types=1);

use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\CreateRole;
use Misaf\VendraPermission\Models\Role;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $tenant = setUpFilamentAdminContextForPermissionModule();
    $this->tenantId = $tenant->id;
});

it('creates a role from the create role schema', function (): void {
    livewire(CreateRole::class)
        ->fillForm([
            'name'        => 'operator',
            'guard_name'  => 'web',
            'description' => 'Can operate the platform',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Role::query()->where([
        'tenant_id'  => $this->tenantId,
        'name'       => 'operator',
        'guard_name' => 'web',
    ])->exists())->toBeTrue();
});

it('validates required fields in create role schema', function (): void {
    livewire(CreateRole::class)
        ->fillForm([
            'name'       => null,
            'guard_name' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name'       => 'required',
            'guard_name' => 'required',
        ]);
});
