<?php
require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

use App\Services\VaultService;
use App\Services\ProductService;
use App\Services\KeyManagementService;
use App\Services\EncryptionService;

$configs = require 'config.php';

$vaultAddress = $configs['vaultAddress'];
$vaultToken = $configs['vaultToken'];
$vaultDbSecretsPath = $configs ['baseVaultPath'] . $configs['vaultDbSecretsPath'];
$vaultSecretKeyPath = $configs ['baseVaultPath'] . $configs['vaultSecretKeyPath'];

$vaultService = new VaultService($vaultAddress, $vaultToken);

echo "Trying to retrieve db secrets from Vault\n";
$dbValues = $vaultService->getSecret($vaultDbSecretsPath);
echo "Successfully retrieved db secrets\n";

$connectionValues = array(
    'dbname' => $dbValues['database'],
    'user' => $dbValues['username'],
    'password' => $dbValues['password'],
    'host' => $dbValues['host'],
    'driver' => $dbValues['driver'],
);

$isDevMode = true;
$dbConfig = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);

$connection = DriverManager::getConnection($connectionValues);
$entityManager = new EntityManager($connection, $dbConfig);

$productService = new ProductService($entityManager);

$keyService = new KeyManagementService($vaultService, $vaultSecretKeyPath);

$encryptionService = new EncryptionService($keyService->getEncryptionKey());

// Registering the listener to the Doctrine EventManager
$entityManager->getEventManager()->addEventListener(
    ['prePersist', 'preUpdate', 'postLoad'],
    $encryptionService
);