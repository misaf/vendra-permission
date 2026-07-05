<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Database\Seeders;

use Illuminate\Support\Facades\Validator;
use Misaf\VendraPermission\Actions\CreateRoleAction;
use Misaf\VendraPermission\Database\Factories\RoleFactory;
use Misaf\VendraSupport\Concerns\RequiresCurrentTenant;
use Misaf\VendraSupport\Database\Seeders\DemoContentSeeder as BaseDemoContentSeeder;
use Misaf\VendraTenant\Models\Tenant;

final class DemoContentSeeder extends BaseDemoContentSeeder
{
    use RequiresCurrentTenant;

    public function __construct(private readonly CreateRoleAction $createRoleAction) {}

    protected function seedFactories(): void
    {
        $tenant = $this->currentTenant();

        $this->seedFactoryRecords($tenant);
    }

    /**
     * @param  list<array<string, mixed>>  $records
     */
    protected function seedFixtures(array $records): void
    {
        $tenant = $this->currentTenant();

        foreach ($records as $record) {
            $this->seedFixtureRecord($tenant, $record);
        }
    }

    protected function seedFactoryRecords(Tenant $tenant): void
    {
        RoleFactory::new()
            ->forTenant($tenant)
            ->createOne();
    }

    /**
     * @param  array<string, mixed>  $record
     */
    protected function seedFixtureRecord(Tenant $tenant, array $record): void
    {
        $data = $this->validatedFixtureRecord($record);

        $this->handleSeedFixtureRecord($tenant, $data);
    }

    /**
     * @param array{
     *     name: string,
     *     description?: string|null,
     *     guard_name: string
     * } $data
     */
    private function handleSeedFixtureRecord(Tenant $tenant, array $data): void
    {
        $this->createRoleAction->execute(
            tenant: $tenant,
            name: $data['name'],
            description: $data['description'] ?? null,
            guardName: $data['guard_name'],
        );
    }

    /**
     * @param  array<string, mixed>  $record
     * @return array{
     *     name: string,
     *     description?: string|null,
     *     guard_name: string
     * }
     */
    private function validatedFixtureRecord(array $record): array
    {
        /** @var array{
         *     name: string,
         *     description?: string|null,
         *     guard_name: string
         * } $validated
         */
        $validated = Validator::make(
            data: $record,
            rules: [
                'name'        => ['required', 'string'],
                'description' => ['nullable', 'string'],
                'guard_name'  => ['required', 'string'],
            ],
        )->validate();

        return $validated;
    }
}
