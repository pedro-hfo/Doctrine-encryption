<?php

namespace App\Services;

use GuzzleHttp\Client;

class VaultService {

    private $client;

    /**
     * VaultService constructor.
     *
     * @param string $vaultAddress The base address of Vault instance.
     * @param string $vaultToken The authentication token to access Vault.
     */
    public function __construct(string $vaultAddress, string $vaultToken) {
        $this->client = new Client([
            'base_uri' => $vaultAddress,
            'headers' => [
                'X-Vault-Token' => $vaultToken
        ]]);
    }

    /**
     * Fetches a secret from Vault.
     *
     * @param string $path The path to the secret in Vault.
     * @return array The secret data.
     */
    public function getSecret(string $path): array {
        $response = $this->client->get($path);
        
        $body = json_decode((string) $response->getBody(), true);
        return $body['data']['data'];
    }

    /**
     * Stores a secret in Vault.
     *
     * @param string $path The path where the secret should be stored in Vault.
     * @param array $data The secret data to store.
     * @return void
     */
    public function putSecret(string $path, array $data): int {
        return $this->client->put($path, ['json' => ['data' => $data]])->getStatusCode();
    }
}
