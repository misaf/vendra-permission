<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters;

use Filament\Clusters\Cluster;
use Laravel\Pennant\Feature;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraTenant\Models\Tenant;

final class PermissionsCluster extends Cluster
{
    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'permissions';

    public static function getNavigationGroup(): string
    {
        return __('navigation.user_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('vendra-permission::navigation.permission');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('navigation.user_management');
    }

    public static function canAccess(): bool
    {
        $tenant = Tenant::current();

        return Feature::for($tenant)->active(PermissionFeatureEnum::MODULE_ENABLED->value);
    }
}
