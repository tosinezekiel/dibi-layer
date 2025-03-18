<?php

return [
    'name' => 'ReposModelsGenerator',

    'paths' => [
        'provider' => [
            'path'      => app_path('Providers'),
            'namespace' => 'App\\Providers\\RepositoryServiceProvider',
        ],
        'models' => [
            'path'      => app_path('Models'),
            'namespace' => 'App\\Models',
        ],
        'read' => [
            'namespace'          => 'App\\Repositories\\Read',
            'path'               => app_path('Repositories/Read'),
            'contract_namespace' => 'App\\Contracts\\Repositories\\Read',
            'contract_path'      => app_path('Contracts/Repositories/Read'),
        ],
        'write' => [
            'namespace'          => 'App\\Repositories\\Write',
            'path'               => app_path('Repositories/Write'),
            'contract_namespace' => 'App\\Contracts\\Repositories\\Write',
            'contract_path'      => app_path('Contracts/Repositories/Write'),
        ],
    ],

    'drivers' => [
        'cache' => [
            'dir' => 'Cache',
        ],
        'dynamodb' => [
            'dir' => 'DynamoDB',
        ],
        'mysql' => [
            'dir' => 'MySql',
        ],
    ],

    'order' => [
        //        'cache',
        //        'dynamodb',
        'mysql',
    ],
];
