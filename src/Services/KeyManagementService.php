<?php

namespace App\Services;

use ParagonIE\Halite\KeyFactory;
use App\Services\VaultService;

class KeyManagementService {
    
    private $vaultService;
    private $vaultKeyPath;

    public function __construct(VaultService $vaultService, string $vaultKeyPath) {
        $this->vaultService = $vaultService;
        $this->vaultKeyPath = $vaultKeyPath;
    }

    /**
     * Fetches the encryption key from Vault.
     * If it doesn't exist, generates a new one, stores it in Vault, and then returns it.
     *
     * @return string
     */
    public function getEncryptionKey(): string {
        try {
            echo "Trying to get key from vault \n";

            $data = $this->vaultService->getSecret($this->vaultKeyPath);
            
            echo "Successfully retrieved key\n";

            return $data['key'];

        } catch (\Exception $e) {
            echo "No key in vault, going to generate a new one and save it there\n";

            $newKey = $this->generateNewKey();
            $this->vaultService->putSecret($this->vaultKeyPath, ['key' => $newKey]);

            echo "Successfully created new key and saved it in vault\n";

            return $newKey;
        }
    }

    private function generateNewKey(): string {
        return KeyFactory::export(KeyFactory::generateEncryptionKey())->getString();
    }
}
