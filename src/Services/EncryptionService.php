<?php

namespace App\Services;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto as SymmetricCrypto;
use ParagonIE\HiddenString\HiddenString;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use App\Product;

class EncryptionService {

    private $key;

    public function __construct(string $rawKey) {
        $this->key = KeyFactory::importEncryptionKey(new HiddenString($rawKey));
    }

    public function encrypt(string $data): string {
        return SymmetricCrypto::encrypt(new HiddenString($data), $this->key);
    }

    public function decrypt(string $ciphertext): string {
        return SymmetricCrypto::decrypt($ciphertext, $this->key)->getString();
    }


    public function prePersist(PrePersistEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof Product) {
            $this->handleEncryption($object);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof Product) {
            $this->handleEncryption($object);
        }
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof Product) {
            $this->handleDecryption($object);
        }
    }

    private function handleEncryption(Product $product): void
    {
        if($product->shouldEncrypt && !$product->isEncrypted()){
            $address = $product->getAddress();
            $product->setAddress($this->encrypt($address));
            $product->setIsEncrypted(true);
        }
    }

    private function handleDecryption(Product $product): void
    {
        if($product->isEncrypted()){
            $address = $product->getAddress();
            $product->setAddress($this->decrypt($address));
            $product->setIsEncrypted(false);
        }

    }
}