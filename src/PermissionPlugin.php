<?php

declare(strict_types=1);

namespace Misaf\VendraPermission;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class PermissionPlugin implements Plugin
{
    public function getId(): string
    {
        return 'vendra-permission';
    }

    public static function make(): static
    {
        /** @var static $plugin */
        $plugin = app(static::class);

        return $plugin;
    }

    public function register(Panel $panel): void
    {
        $panel->discoverClusters(
            in: __DIR__ . '/Filament/Clusters',
            for: 'Misaf\\VendraPermission\\Filament\\Clusters',
        );
    }

    public function boot(Panel $panel): void {}
}
