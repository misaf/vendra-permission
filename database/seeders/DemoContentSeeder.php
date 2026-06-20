<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Seeders;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Misaf\VendraPermission\Actions\CreateRoleAction;
use Misaf\VendraPermission\Database\Factories\RoleFactory;
use Misaf\VendraSupport\Database\Seeders\TenantDemoContentSeeder;
use Misaf\VendraTenant\Models\Tenant;

final class DemoContentSeeder extends TenantDemoContentSeeder
{
    public function __construct(private readonly CreateRoleAction $createRoleAction) {}

    protected function seedFactoryRecords(Tenant $tenant): void
    {
        RoleFactory::new()
            ->forTenant($tenant)
            ->forGuard(Config::string('auth.defaults.guard', 'web'))
            ->create([
                'name' => Config::string('vendra-permission.super_admin_role', 'super-admin'),
            ]);
    }

    protected function seedFixtureRecord(Tenant $tenant, array $record): void
    {
        $data = $this->validatedFixtureRecord($record);

        $this->handleSeedFixtureRecord($tenant, $data);
    }

    /**
     * @param array{name: string, description: string, guard_name: string} $data
     */
    private function handleSeedFixtureRecord(Tenant $tenant, array $data): void
    {
        $role = $this->createRoleAction->execute(
            $tenant,
            $data['name'],
            $data['guard_name'],
        );

        $role->fill([
            'description' => $data['description'],
        ]);

        $role->save();
    }

    /**
     * @param array<string, mixed> $record
     *
     * @return array{name: string, description: string, guard_name: string}
     */
    private function validatedFixtureRecord(array $record): array
    {
        /** @var array{name: string, description: string, guard_name: string} $validated */
        $validated = Validator::make(
            data: $record,
            rules: [
                'name'        => ['required', 'string'],
                'description' => ['required', 'string'],
                'guard_name'  => ['required', 'string'],
            ],
        )->validate();

        return $validated;
    }
}
