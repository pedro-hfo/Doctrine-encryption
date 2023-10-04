<?php

/*
Vault credentials example for local server
    Address - 'http://127.0.0.1:8200'
    Root token - 'hvs.Vjo3S8ic1qJsOmXoVjfbvien' - given when initializing server
    Key path - '/v1/secret/data/phpapp/encryption' - the part after data/ can be freely modified
*/
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