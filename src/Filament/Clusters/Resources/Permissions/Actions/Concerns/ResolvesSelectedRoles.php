<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions\Concerns;

use InvalidArgumentException;
use Misaf\VendraPermission\Models\Role;

trait ResolvesSelectedRoles
{
    /**
     * @return array<ModelKey, string>
     */
    private function getRoleSelectOptions(): array
    {
        return Role::query()
            ->orderBy('name')
            ->orderBy('guard_name')
            ->get(['id', 'name', 'guard_name'])
            ->mapWithKeys(static function (Role $role): array {
                return [$role->id => "{$role->name} ({$role->guard_name})"];
            })
            ->all();
    }

    /**
     * @param array{roles?: mixed, ...} $data
     * @return array<string, list<ModelKey>>
     */
    private function resolveRoleIdsByGuardFromPayload(array $data): array
    {
        $rawRoleIds = $data['roles'] ?? null;

        if ( ! is_array($rawRoleIds)) {
            throw new InvalidArgumentException('Invalid roles provided.');
        }

        $roleIds = [];

        foreach ($rawRoleIds as $rawRoleId) {
            if (is_int($rawRoleId) || is_string($rawRoleId)) {
                $roleIds[] = $rawRoleId;
            }
        }

        if ([] === $roleIds) {
            throw new InvalidArgumentException('Invalid roles provided.');
        }

        return Role::query()
            ->whereKey($roleIds)
            ->get(['id', 'guard_name'])
            ->groupBy('guard_name')
            ->map(static function ($roles): array {
                return $roles->pluck('id')->values()->all();
            })
            ->all();
    }
}
