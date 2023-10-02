<?php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Service\EncryptionService;
use App\Service\ProductService;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);

// Database connection parameters
$conn = array(
    'dbname' => 'testdb',
    'user' => 'test',
    'password' => '123.',
    'host' => 'localhost',
    'driver' => 'pdo_pgsql',
);

$entityManager = EntityManager::create($conn, $config);

// Initialize the Encryption Service
$keyPath = __DIR__ . '/test_key.key';
$encryptionService = new EncryptionService($keyPath);
$productService = new ProductService($entityManager, $encryptionService);