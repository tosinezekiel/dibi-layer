<?php

return [
    'name' => 'ReposModelsGenerator',

    'paths' => [
        'domain' => [
            'path' => app_path('Domain'),
            'namespace' => 'App\\Domain'],
        'provider' => [
            'path'      => app_path('Providers'),
            'namespace' => 'App\\Providers\\RepositoryServiceProvider',
        ],
        'models' => [
            'path'      => app_path('Models'),
            'namespace' => 'Models',
        ],
        'read' => [
            'namespace'          => 'Repositories\\Read',
            'path'               => app_path('Repositories/Read'),
            'contract_namespace' => 'Contracts\\Repositories\\Read',
            'contract_path'      => app_path('Contracts/Repositories/Read'),
        ],
        'write' => [
            'namespace'          => 'Repositories\\Write',
            'path'               => app_path('Repositories/Write'),
            'contract_namespace' => 'Contracts\\Repositories\\Write',
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
