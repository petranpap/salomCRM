<?php

return [
    'tenant' => [
        'model' => \Stancl\Tenancy\Database\Models\Tenant::class,
        'database' => [
            'connection' => 'mysql',
            'prefix' => '',
            'suffix' => '',
        ],
        'routes' => [
            'web' => [
                'prefix' => '',
                'middleware' => ['web', 'tenant'],
            ],
            'api' => [
                'prefix' => 'api',
                'middleware' => ['api', 'tenant'],
            ],
        ],
    ],

    'features' => [
        'centralized' => true,
        'subdomain' => true,
        'domain' => false,
    ],

    'storage' => [
        'prefix' => 'tenants',
    ],

    'cache' => [
        'enabled' => true,
        'store' => 'redis',
    ],

    'queue' => [
        'enabled' => true,
        'connection' => 'redis',
    ],

    'backup' => [
        'enabled' => true,
        'schedule' => 'daily',
        'path' => storage_path('backups'),
    ],
];