<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Laravel\Pennant\Feature;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraPermission\Filament\Clusters\PermissionsCluster;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\CreatePermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\EditPermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ListPermissions;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Pages\ViewPermission;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Schemas\PermisssionForm;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Tables\PermissionTable;
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraTenant\Models\Tenant;

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

    public static function canAccess(): bool
    {
        $tenant = Tenant::current();

        return Feature::for($tenant)->active(PermissionFeatureEnum::MODULE_ENABLED->value)
            && Feature::for($tenant)->active(PermissionFeatureEnum::PERMISSION_MANAGEMENT->value);
    }
}
