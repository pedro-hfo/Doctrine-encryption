<?php

namespace App\Util;

class DbSecretsHandler {

    private $vaultService;
    private $databaseSecrets;

    public function __construct($vaultService, $databaseSecrets) {
        $this->vaultService = $vaultService;
        $this->databaseSecrets = $databaseSecrets;
    }

    public function storeSecrets() {
        try {
            $statusCode = $this->vaultService->putSecret('/v1/secret/data/phpapp/database-config', $this->databaseSecrets);

            if ($statusCode == 200) {
                echo "Database secrets stored successfully.\n";
                return $this->databaseSecrets;
            } else {
                echo "Failed to store database secrets.\n";
            }

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }

        return null;
    }

}