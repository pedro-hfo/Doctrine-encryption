<?php

namespace App\Service;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto as SymmetricCrypto;
use ParagonIE\HiddenString\HiddenString;

class EncryptionService {

    private $key;

    public function __construct(string $keyPath) {
        $this->key = KeyFactory::loadEncryptionKey($keyPath);
    }

    public function encrypt(string $data): string {
        return SymmetricCrypto::encrypt(new HiddenString($data), $this->key);
    }

    public function decrypt(string $ciphertext): string {
        return SymmetricCrypto::decrypt($ciphertext, $this->key)->getString();
    }

    public function isEncrypted(string $data): bool {
        // Implement your logic here to determine if a string is encrypted.
        // Be cautious with this implementation, as identifying encrypted data
        // can sometimes be non-trivial, and misclassifying can have consequences.
        return false;
    }
}