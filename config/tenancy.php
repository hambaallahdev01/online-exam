<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Tenant Timezone
    |--------------------------------------------------------------------------
    |
    | Tenant-facing dates are interpreted and displayed in the school's IANA
    | timezone. Persisted timestamps and all schedule comparisons remain UTC.
    |
    */

    'default_timezone' => env('TENANT_DEFAULT_TIMEZONE', 'Asia/Jakarta'),

];
