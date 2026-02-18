# Vendra Permission

Role and permission management for Vendra, built on top of Spatie Permission and Filament 4.

## Features

- Filament cluster on the `admin` panel for permission management
- Role and permission CRUD resources
- Manage role-permission relations from role pages
- Tenant-scoped Pennant feature flags for module/resource access
- Feature toggle Artisan command per tenant
- Policy classes and enums for role/permission actions
- Translation files for `en` and `fa`
- Configurable `Gate::after()` superadmin bypass role

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 4
- `laravel/pennant`
- `misaf/vendra-tenant`
- `misaf/vendra-user`
- `misaf/vendra-activity-log`
- `awcodes/filament-badgeable-column`
- `mokhosh/filament-jalali`
- `spatie/laravel-permission`

## Installation

```bash
composer require misaf/vendra-permission
```

Publish Spatie permission config and migrations (if not already published in your app):

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag=permission-config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag=permission-migrations
php artisan migrate
```

Optional: publish module translations:

```bash
php artisan vendor:publish --tag=vendra-permission-translations
```

Optional: publish module config:

```bash
php artisan vendor:publish --tag=vendra-permission-config
```

If you use Pennant with the database driver, ensure Pennant storage is migrated in your application.

## Configuration

If you want Spatie to use this module's models everywhere in your app, set `config/permission.php`:

```php
'models' => [
    'permission' => Misaf\VendraPermission\Models\Permission::class,
    'role' => Misaf\VendraPermission\Models\Role::class,
],
```

Superadmin bypass role is configurable in `config/vendra-permission.php`:

```php
'super_admin_role' => env('VENDRA_PERMISSION_SUPER_ADMIN_ROLE', 'superadmin'),
```

The config key is `super_admin_role` (short and local to this file).  
The env var remains `VENDRA_PERMISSION_SUPER_ADMIN_ROLE` (prefixed to avoid global collisions).
This value is used for both `Gate::after()` superadmin bypass and excluding that role from the roles table query.

Pennant feature behavior is configured in `config/vendra-permission.php`:

```php
'features' => [
    'enabled' => env('VENDRA_PERMISSION_FEATURES_ENABLED', false),
    'discover' => env('VENDRA_PERMISSION_FEATURES_DISCOVER', false),
    'defaults' => [
        'vendra-permission.module-enabled' => false,
        'vendra-permission.role-management' => false,
        'vendra-permission.permission-management' => false,
        'vendra-permission.bulk-role-assignment' => false,
    ],
],
```

When `features.discover` is enabled, the package calls:

```php
Feature::discover('Misaf\\VendraPermission\\Features', __DIR__ . '/Features');
```

if the directory exists.

## Filament

Resources are registered on the `admin` panel through `PermissionPlugin`:

- Roles
- Permissions

Navigation cluster: `permissions`

Access is feature-gated per tenant using `Feature::for(Tenant::current())`:

- `vendra-permission.module-enabled` controls cluster access
- `vendra-permission.role-management` controls role resource access
- `vendra-permission.permission-management` controls permission resource access
- `vendra-permission.bulk-role-assignment` controls attach/detach role bulk actions

### Permission Bulk Actions

`AttachRolesAction` and `DetachRolesAction` share role-resolution logic via:

- `Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions\Concerns\ResolvesSelectedRoles`

The trait provides:

- `getRoleSelectOptions()` for the roles multiselect options (`name (guard_name)`)
- `resolveRoleIdsByGuardFromPayload()` to load selected roles with `whereKey(...)` and group by `guard_name`

This keeps attach/detach behavior consistent and prevents cross-guard role operations when processing selected `Permission` records.

## Pennant Features

Feature keys are defined in:

- `Misaf\VendraPermission\Enums\PermissionFeatureEnum`

Resolver registration lives in:

- `Misaf\VendraPermission\PermissionServiceProvider::packageBooted()`

Resolver behavior:

- non-tenant scopes are denied (`false`)
- `features.enabled` must be true
- unresolved values fall back to `features.defaults`

Feature map:

| Enum case | Feature key | Short key | Effect |
| --- | --- | --- | --- |
| `MODULE_ENABLED` | `vendra-permission.module-enabled` | `module-enabled` | Enables or hides the whole permissions cluster |
| `ROLE_MANAGEMENT` | `vendra-permission.role-management` | `role-management` | Enables or hides the roles resource |
| `PERMISSION_MANAGEMENT` | `vendra-permission.permission-management` | `permission-management` | Enables or hides the permissions resource |
| `BULK_ROLE_ASSIGNMENT` | `vendra-permission.bulk-role-assignment` | `bulk-role-assignment` | Enables or hides attach/detach role bulk actions |

## Artisan Commands

Toggle tenant features with:

```bash
php artisan vendra-permission:feature {activate|deactivate} {feature|all} {tenant}
```

`feature` accepts:

- enum case name, e.g. `ROLE_MANAGEMENT`
- full key, e.g. `vendra-permission.role-management`
- short key, e.g. `role-management`
- `all`

`tenant` accepts:

- tenant `id`
- tenant `slug`

The command requires `vendra-permission.features.enabled` to be `true`.

Examples:

```bash
php artisan vendra-permission:feature activate module-enabled 1
php artisan vendra-permission:feature activate permission-management acme
php artisan vendra-permission:feature deactivate all acme
```

Per-feature console reference (`<tenant>` can be tenant `id` or `slug`):

```bash
# MODULE_ENABLED
php artisan vendra-permission:feature activate MODULE_ENABLED <tenant>
php artisan vendra-permission:feature deactivate MODULE_ENABLED <tenant>

# ROLE_MANAGEMENT
php artisan vendra-permission:feature activate ROLE_MANAGEMENT <tenant>
php artisan vendra-permission:feature deactivate ROLE_MANAGEMENT <tenant>

# PERMISSION_MANAGEMENT
php artisan vendra-permission:feature activate PERMISSION_MANAGEMENT <tenant>
php artisan vendra-permission:feature deactivate PERMISSION_MANAGEMENT <tenant>

# BULK_ROLE_ASSIGNMENT
php artisan vendra-permission:feature activate BULK_ROLE_ASSIGNMENT <tenant>
php artisan vendra-permission:feature deactivate BULK_ROLE_ASSIGNMENT <tenant>
```

## Usage

Use this package's tenant feature flags and console command to control access in each tenant:

- `vendra-permission.module-enabled`
- `vendra-permission.role-management`
- `vendra-permission.permission-management`
- `vendra-permission.bulk-role-assignment`

Role/permission CRUD and assignment semantics follow Spatie Permission.  
See: https://spatie.be/docs/laravel-permission

## Development

```bash
composer test
composer analyse
composer format
```

## Keeping This README Updated

Update this file whenever these change:

- `composer.json` requirements
- Pennant feature keys, defaults, or resolver behavior
- feature command signature or behavior
- Filament resources, pages, or cluster names
- Translation keys/files in `resources/lang`
- Authorization behavior in policies or `PermissionServiceProvider`
- Installation steps (especially migration/config behavior)

## License

MIT. See [LICENSE](LICENSE).
