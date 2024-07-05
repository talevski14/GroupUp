<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'name' => 'groupup',
            'user' => 'groupup',
            'pass' => 'secret',
            'host' => 'localhost',
            'port' => '3306',
            'charset' => "utf8mb4",
        ],
    ],
    'version_order' => 'creation'
];
