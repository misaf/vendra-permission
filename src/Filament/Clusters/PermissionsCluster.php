<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use Laravel\Pennant\Feature;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraSupport\Contracts\TenantResolver;
use Misaf\VendraSupport\Filament\Navigation\NavigationGroup;

final class PermissionsCluster extends Cluster
{
    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'permissions';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    public static function getNavigationGroup(): string
    {
        return NavigationGroup::Customers->getLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function canAccess(): bool
    {
        $tenant = app(TenantResolver::class)->current();

        return Feature::for($tenant)->active(PermissionFeatureEnum::MODULE_ENABLED->value);
    }
}
