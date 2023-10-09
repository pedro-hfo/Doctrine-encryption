<?php

namespace App\Services;

use GuzzleHttp\Client;

class VaultService
{

    private $client;
    private $vaultToken;
    private $roleName;
    private $roleId;

    public function __construct(string $vaultAddress, string $vaultToken, string $roleName, string $roleId)
    {
        $this->client = new Client(['base_uri' => $vaultAddress]);
        $this->vaultToken = $vaultToken;
        $this->roleId = $roleId;
        $this->roleName = $roleName;
    }

    /**
     * Authenticates with Vault using AppRole, retrieves a token and returns it.
     *
     * @return string The Vault token.
     */
    private function getAuthenticationToken(): string
    {
        $response = $this->client->post('/v1/auth/approle/login', [
            'json' => [
                'role_id' => $this->roleId,
                'secret_id' => $this->generateSecretId()
            ]
        ]);

        $body = json_decode((string)$response->getBody(), true);
        return $body['auth']['client_token'];
    }

    public function generateSecretId(): string
    {
        $url = "/v1/auth/approle/role/{$this->roleName}/secret-id";
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'X-Vault-Token' => $this->vaultToken,
                ],
            ]);
            $body = json_decode((string) $response->getBody(), true);
            return $body['data']['secret_id'];
        } catch (\Exception $e) {
            throw new \Exception("Failed to generate SecretID: " . $e->getMessage());
        }
    }

    /**
     * Fetches a secret from Vault.
     *
     * @param string $path The path to the secret in Vault.
     * @return array The secret data.
     * 
     * @return array The retrieved secret.
     */
    public function getSecret(string $path): array
    {
        $response = $this->client->get($path, ['headers' => ['X-Vault-Token' => $this->getAuthenticationToken()]]);
        $body = json_decode((string) $response->getBody(), true);
        return $body['data']['data'];
    }

    /**
     * Stores a secret in Vault.
     *
     * @param string $path The path where the secret should be stored in Vault.
     * @param array $data The secret data to store.
     * 
     * @return int The HTTP status code.
     */
    public function putSecret(string $path, array $data): int
    {
        return $this->client->put(
            $path,
            [
                'json' => ['data' => $data],
                'headers' => ['X-Vault-Token' => $this->getAuthenticationToken()]
            ]
        )->getStatusCode();
    }
}
