<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Super Admin Role
    |--------------------------------------------------------------------------
    |
    | This role will bypass authorization checks in Gate::after().
    |
    */

    'super_admin_role' => env('VENDRA_PERMISSION_SUPER_ADMIN_ROLE', 'super-admin'),

    /*
    |--------------------------------------------------------------------------
    | Pennant Features
    |--------------------------------------------------------------------------
    |
    | Permission module features are tenant-scoped and resolved through
    | Laravel Pennant.
    |
    */

    'features' => [
        'enabled' => env('VENDRA_PERMISSION_FEATURES_ENABLED', false),

        'discover' => env('VENDRA_PERMISSION_FEATURES_DISCOVER', false),

        'defaults' => [
            'vendra-permission.module-enabled'        => false,
            'vendra-permission.role-management'       => false,
            'vendra-permission.permission-management' => false,
            'vendra-permission.bulk-role-assignment'  => false,
        ],
    ],

];
