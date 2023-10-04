<?php

return [
    'vaultAddress' => 'http://127.0.0.1:8200',
    'vaultToken' => 'hvs.SUIpZjA8c1mJFutNlcZsOQgV',
    'baseVaultPath' => '/v1/secret/data/phpapp/',
    'vaultDbSecretsPath' => 'database-config',
    'vaultSecretKeyPath' => 'encryption-key',
    'databaseSecrets' => [
        'database' => 'yourdbname',
        'username' => 'yourdbuser',
        'password' => 'yourdbpassword',
        'host' => 'localhost',
        'driver' => 'pdo_pgsql'
    ]
];