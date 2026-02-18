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

    'super_admin_role' => env('VENDRA_PERMISSION_SUPER_ADMIN_ROLE', 'superadmin'),

];
