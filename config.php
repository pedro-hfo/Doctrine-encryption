<?php

return [
    'vaultAddress' => 'http://127.0.0.1:8200',
    'vaultToken' => 'hvs.rveKCwef2HSPS3ej3UCV3mSU',
    'vaultDbSecretsPath' => '/v1/secret/data/phpapp/database-config',
    'vaultSecretKeyPath' => '/v1/secret/data/phpapp/encryption-key',
    'databaseSecrets' => [
        'database' => 'yourdbname',
        'username' => 'yourdbuser',
        'password' => 'yourdbpassword',
        'host' => 'localhost',
        'driver' => 'pdo_pgsql'
    ]
];