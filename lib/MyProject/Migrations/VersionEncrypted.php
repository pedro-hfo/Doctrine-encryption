<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class VersionEncrypted extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $conn = $this->connection;
        
        require_once './src/Service/EncryptionService.php';
        
        $keyPath = './test_key.key';
        $encryptionService = new \App\Service\EncryptionService($keyPath);
        
        // Fetch All Products
        $stmt = $conn->executeQuery('SELECT id, address, is_encrypted FROM products');
        
        while ($row = $stmt->fetchAssociative()) {
            $id = $row['id'];
            $address = $row['address'];
            $isEncrypted = $row['is_encrypted'];
            
            // Check if the address is already encrypted
            if (!$isEncrypted) {
                $encryptedAddress = $encryptionService->encrypt($address);
                $conn->update('products', ['address' => $encryptedAddress, 'is_encrypted' => 1], ['id' => $id]);
            }
        }
    }

    public function down(Schema $schema): void
    {
        $conn = $this->connection;
        
        require_once './src/Service/EncryptionService.php';
        
        $keyPath = './test_key.key';
        $encryptionService = new \App\Service\EncryptionService($keyPath);
        
        // Fetch All Products
        $stmt = $conn->executeQuery('SELECT id, address, is_encrypted FROM products');
        
        while ($row = $stmt->fetchAssociative()) {
            $id = $row['id'];
            $address = $row['address'];
            $isEncrypted = $row['is_encrypted'];
            
            // Check if the address is already encrypted
            if ($isEncrypted) {
                $unencryptedAddress = $encryptionService->decrypt($address);
                $conn->update('products', ['address' => $unencryptedAddress, 'is_encrypted' => 0], ['id' => $id]);
            }
        }
    }
}
