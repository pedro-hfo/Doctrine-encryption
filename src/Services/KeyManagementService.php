<?php

namespace App\Services;

use GuzzleHttp\Client;
use ParagonIE\Halite\KeyFactory;

class KeyManagementService {
    
    private $vaultAddress;
    private $vaultKeyPath;
    private $client;

    public function __construct(string $vaultAddress, string $vaultToken, string $vaultKeyPath) {
        $this->vaultAddress = $vaultAddress;
        $this->vaultKeyPath = $vaultKeyPath;
        $this->client = new Client([
            'base_uri' => $this->vaultAddress,
            'headers' => [
                'X-Vault-Token' => $vaultToken
        ]]);
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

            $response = $this->client->get($this->vaultKeyPath);

            $body = json_decode((string) $response->getBody(), true);
            $key = $body['data']['data']['key'];
            
            echo "Successfully retrieved key\n";

            return $key;

        } catch (\Exception $e) {
            echo "No key in vault, going to generate a new one and save it there\n";

            $newKey = $this->generateNewKey();
            $this->storeKeyInVault($newKey);

            echo "Successfully created new key and saved it in vault\n";

            return $newKey;
        }
    }

    private function generateNewKey(): string {
        return KeyFactory::export(KeyFactory::generateEncryptionKey())->getString();
    }

    /**
     * Stores the given encryption key in Vault.
     *
     * @param string $key
     * @return void
     */
    private function storeKeyInVault(string $key): void {
        $this->client->put($this->vaultKeyPath, [
            'json' => [
                'data' => ['key' => $key]
            ]
        ]);
    }
}
