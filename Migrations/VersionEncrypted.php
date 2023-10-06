<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

use App\Services\VaultService;
use App\Services\KeyManagementService;
use App\Services\EncryptionService;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class VersionEncrypted extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration to encrypt/decrypt address on all rows';
    }

    public function up(Schema $schema) : void
    {
        $encryptionService = new EncryptionService($this->getEncryptionKey());

        $conn = $this->connection;
        
        // Fetch All Products
        $stmt = $conn->executeQuery('SELECT id, address, is_encrypted FROM products');
        
        while ($row = $stmt->fetchAssociative()) {
            $id = $row['id'];
            $address = $row['address'];
            $isEncrypted = $row['is_encrypted'];
            
            // Only do this if address isn't encrypted
            if (!$isEncrypted) {
                $encryptedAddress = $encryptionService->encrypt($address);
                $conn->update('products', ['address' => $encryptedAddress, 'is_encrypted' => 1], ['id' => $id]);
            }
        }
    }

    public function down(Schema $schema): void
    {
        $encryptionService = new EncryptionService($this->getEncryptionKey());

        $conn = $this->connection;
        
        // Fetch All Products
        $stmt = $conn->executeQuery('SELECT id, address, is_encrypted FROM products');
        
        while ($row = $stmt->fetchAssociative()) {
            $id = $row['id'];
            $address = $row['address'];
            $isEncrypted = $row['is_encrypted'];
            
            // Only do this if address isn't encrypted
            if ($isEncrypted) {
                $unencryptedAddress = $encryptionService->decrypt($address);
                $conn->update('products', ['address' => $unencryptedAddress, 'is_encrypted' => 0], ['id' => $id]);
            }
        }
    }

    private function getEncryptionKey(): string {
        $configs = require_once 'config.php';
        $vaultAddress = $configs['vaultAddress'];
        $roleName = $configs['roleName'];
        $roleId = $configs['roleId'];
        $vaultSecretKeyPath = $configs ['baseVaultPath'] . $configs['vaultSecretKeyPath'];
        $vaultToken = getenv('VAULT_TOKEN');

        $vaultService = new VaultService($vaultAddress, $vaultToken, $roleName, $roleId);
        $keyService = new  KeyManagementService($vaultService, $vaultSecretKeyPath);
        return $keyService->getEncryptionKey();
    }
}