<?php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Services\EncryptionService;
use App\Services\ProductService;

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

// Initialize the Encryption Service
$keyPath = __DIR__ . '/test_key.key';
$encryptionService = new EncryptionService($keyPath);

// Registering the listener to the Doctrine EventManager
$entityManager->getEventManager()->addEventListener(
    ['prePersist', 'preUpdate', 'postLoad'],
    $encryptionService
);