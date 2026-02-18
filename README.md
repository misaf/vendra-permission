# Vendra Permission

Role and permission management for Vendra, built on top of Spatie Permission and Filament 4.

## Features

- Filament cluster on the `admin` panel for permission management
- Role and permission CRUD resources
- Manage role-permission relations from role pages
- Policy classes and enums for role/permission actions
- Translation files for `en` and `fa`
- Configurable `Gate::after()` superadmin bypass role

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 4
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

## Filament

Resources are registered on the `admin` panel through `PermissionPlugin`:

- Roles
- Permissions

Navigation cluster: `permissions`

### Permission Bulk Actions

`AttachRolesAction` and `DetachRolesAction` share role-resolution logic via:

- `Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions\Concerns\ResolvesSelectedRoles`

The trait provides:

- `getRoleOptions()` for the roles multiselect options (`name (guard_name)`)
- `getRolesByGuard()` to load selected roles with `whereKey(...)` and group by `guard_name`

This keeps attach/detach behavior consistent and prevents cross-guard role operations when processing selected `Permission` records.

## Usage

Create a role and permission:

```php
use Misaf\VendraPermission\Models\Permission;
use Misaf\VendraPermission\Models\Role;

$role = Role::query()->create([
    'name' => 'editor',
    'guard_name' => 'web',
]);

$permission = Permission::query()->create([
    'name' => 'view-any-post',
    'guard_name' => 'web',
]);

$role->givePermissionTo($permission);
```

Assign role to a user:

```php
$user->assignRole('editor');
```

## Development

```bash
composer test
composer analyse
composer format
```

## Keeping This README Updated

Update this file whenever these change:

- `composer.json` requirements
- Filament resources, pages, or cluster names
- Translation keys/files in `resources/lang`
- Authorization behavior in policies or `PermissionServiceProvider`
- Installation steps (especially migration/config behavior)

## License

MIT. See [LICENSE](LICENSE).
