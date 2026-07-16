<?php

declare(strict_types=1);

namespace Misaf\VendraPermission;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class PermissionPlugin implements Plugin
{
    public const string ID = 'vendra-permission';

    public function getId(): string
    {
        return self::ID;
    }

    public static function make(): static
    {
        /** @var static $plugin */
        $plugin = app(self::class);

        return $plugin;
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Clusters/Resources',
            for: 'Misaf\\VendraPermission\\Filament\\Clusters\\Resources',
        );
    }

    public function boot(Panel $panel): void {}
}
