<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Roles;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Misaf\VendraPermission\Filament\Clusters\PermissionsCluster;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\RelationManagers\PermissionRelationManager;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\CreateRole;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\EditRole;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\ListRoles;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Pages\ViewRole;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Schemas\RoleForm;
use Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Tables\RoleTable;
use Misaf\VendraPermission\Models\Role;

final class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'roles';

    protected static ?string $cluster = PermissionsCluster::class;

    public static function getBreadcrumb(): string
    {
        return __('vendra-permission::navigation.role');
    }

    public static function getModelLabel(): string
    {
        return __('vendra-permission::navigation.role');
    }

    public static function getNavigationGroup(): string
    {
        return __('vendra-permission::navigation.permission_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('vendra-permission::navigation.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vendra-permission::navigation.role');
    }

    public static function getRelations(): array
    {
        return [
            PermissionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view'   => ViewRole::route('/{record}'),
            'edit'   => EditRole::route('/{record}/edit'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleTable::configure($table);
    }
}
