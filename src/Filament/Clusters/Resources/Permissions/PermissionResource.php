<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Misaf\VendraPermission\Filament\Clusters\PermissionsCluster;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\CreatePermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\EditPermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ListPermissions;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ViewPermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Schemas\PermisssionForm;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Tables\PermissionTable;
use Misaf\VendraPermission\Models\Permission;

final class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'permissions';

    protected static ?string $cluster = PermissionsCluster::class;

    public static function getBreadcrumb(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getModelLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getNavigationGroup(): string
    {
        return __('vendra-permission::navigation.permission_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view'   => ViewPermission::route('/{record}'),
            'edit'   => EditPermission::route('/{record}/edit'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return PermisssionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionTable::configure($table);
    }
}
