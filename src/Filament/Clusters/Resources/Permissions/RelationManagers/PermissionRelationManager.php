<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\PermissionResource;
use Misaf\VendraPermission\Models\Permission;

final class PermissionRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    public static function getModelLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): string
    {
        /** @var Collection<int, Permission> $permissions */
        $permissions = $ownerRecord->getRelation('permissions') ?? collect();

        return (string) Number::format($permissions->count());
    }

    public function form(Schema $schema): Schema
    {
        return PermissionResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return PermissionResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['roles'] = $this->getOwnerRecord()->id;
                        $data['guard_name'] = $this->getOwnerRecord()->guard_name;

                        return $data;
                    }),
            ]);
    }
}
