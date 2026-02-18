<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Misaf\VendraPermission\Enums\PermissionFeatureEnum;
use Misaf\VendraTenant\Models\Tenant;

final class FeatureToggleCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'vendra-permission:feature
        {action : activate or deactivate}
        {feature : Feature case, value, short value, or all}
        {tenant : Tenant ID or slug}';

    /**
     * @var string
     */
    protected $description = 'Activate or deactivate a vendra-permission feature for a tenant';

    public function handle(): int
    {
        $actionInput = $this->getStringArgument('action');

        if (null === $actionInput) {
            $this->error('Argument [action] must be a string.');

            return self::INVALID;
        }

        $action = Str::lower($actionInput);

        if ( ! in_array($action, ['activate', 'deactivate'], true)) {
            $this->error('Action must be one of: activate, deactivate.');

            return self::INVALID;
        }

        if ( ! Config::boolean('vendra-permission.features.enabled', false)) {
            $this->error('Feature resolution is disabled via vendra-permission.features.enabled.');
            $this->info('Set VENDRA_PERMISSION_FEATURES_ENABLED=true and try again.');

            return self::INVALID;
        }

        $tenantInput = $this->getStringArgument('tenant');

        if (null === $tenantInput) {
            $this->error('Argument [tenant] must be a string.');

            return self::INVALID;
        }

        $tenant = $this->resolveTenant($tenantInput);

        if ( ! $tenant instanceof Tenant) {
            $this->error("Tenant [{$tenantInput}] was not found.");

            return self::FAILURE;
        }

        $featureInput = $this->getStringArgument('feature');

        if (null === $featureInput) {
            $this->error('Argument [feature] must be a string.');

            return self::INVALID;
        }

        $features = $this->resolveFeatures($featureInput);

        if ([] === $features) {
            $this->error("Feature [{$featureInput}] is invalid.");
            $this->info('Accepted values:');

            foreach (PermissionFeatureEnum::cases() as $feature) {
                $this->line("- {$feature->name} ({$feature->value})");
            }

            $this->line('- all');

            return self::INVALID;
        }

        $interaction = Feature::for($tenant);

        foreach ($features as $feature) {
            if ('activate' === $action) {
                $interaction->activate($feature->value);
            } else {
                $interaction->deactivate($feature->value);
            }
        }

        $this->info(sprintf(
            'Tenant [%s] (%d): %s %d feature(s).',
            $tenant->slug,
            $tenant->id,
            'activate' === $action ? 'activated' : 'deactivated',
            count($features),
        ));

        $this->table(
            ['Feature', 'Status'],
            array_map(
                static fn(PermissionFeatureEnum $feature): array => [
                    $feature->value,
                    'activate' === $action ? 'active' : 'inactive',
                ],
                $features,
            ),
        );

        return self::SUCCESS;
    }

    private function resolveTenant(string $tenantInput): ?Tenant
    {
        if (is_numeric($tenantInput)) {
            /** @var Tenant|null $tenant */
            $tenant = Tenant::query()->find((int) $tenantInput);

            return $tenant;
        }

        /** @var Tenant|null $tenant */
        $tenant = Tenant::query()
            ->where('slug', $tenantInput)
            ->first();

        return $tenant;
    }

    /**
     * @return array<int, PermissionFeatureEnum>
     */
    private function resolveFeatures(string $featureInput): array
    {
        $normalizedInput = Str::of($featureInput)->trim()->lower()->value();

        if ('all' === $normalizedInput) {
            return PermissionFeatureEnum::cases();
        }

        $resolved = array_values(array_filter(
            PermissionFeatureEnum::cases(),
            function (PermissionFeatureEnum $feature) use ($normalizedInput): bool {
                $normalizedCaseName = Str::lower($feature->name);
                $normalizedValue = Str::lower($feature->value);
                $shortValue = Str::lower(Str::replaceFirst('vendra-permission.', '', $feature->value));

                return $normalizedInput === $normalizedCaseName
                    || $normalizedInput === $normalizedValue
                    || $normalizedInput === $shortValue;
            }
        ));

        return $resolved;
    }

    private function getStringArgument(string $name): ?string
    {
        $argument = $this->argument($name);

        return is_string($argument) ? $argument : null;
    }
}
