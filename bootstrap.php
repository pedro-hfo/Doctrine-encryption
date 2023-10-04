<?php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Services\EncryptionService;
use App\Services\ProductService;
use App\Services\KeyManagementService;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);

// Database connection parameters
$conn = array(
    'dbname' => 'yourdbname',
    'user' => 'yourdbuser',
    'password' => 'yourdbpassword',
    'host' => 'localhost',
    'driver' => 'pdo_pgsql',
);

$entityManager = EntityManager::create($conn, $config);

$productService = new ProductService($entityManager);

/*
Vault credentials example for local server
    Address - 'http://127.0.0.1:8200'
    Root token - 'hvs.Vjo3S8ic1qJsOmXoVjfbvien' - given when initializing server
    Vault key path - '/v1/secret/data/phpapp/encryption' - the part after data/ can be freely modified
*/
$keyService = new KeyManagementService('vaultAddressHere', 'rootTokenHere', 'vaultKeyPathHere');

$encryptionService = new EncryptionService($keyService->getEncryptionKey());

// Registering the listener to the Doctrine EventManager
$entityManager->getEventManager()->addEventListener(
    ['prePersist', 'preUpdate', 'postLoad'],
    $encryptionService
);