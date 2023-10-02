<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Product;

class ProductService {

    private $entityManager;
    private $encryptionService;

    public function __construct(EntityManager $entityManager, EncryptionService $encryptionService) {
        $this->entityManager = $entityManager;
        $this->encryptionService = $encryptionService;
    }

    public function createProduct(string $name, string $address, bool $encryptAddress = true) {
        $product = new Product();
        $product->setName($name);

        $processedAddress = $encryptAddress ? $this->encryptionService->encrypt($address) : $address;
        $product->setAddress($processedAddress);

        $product->setIsEncrypted($encryptAddress);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    
        return $product->getId();
    }

    public function getProductById($id, $decryptAddress = true) {
        $productRepo = $this->entityManager->getRepository(Product::class);
        $product = $productRepo->find($id);

        if (!$product) {
            return null;
        }
        
        if ($decryptAddress) {
            $decryptedAddress = $this->encryptionService->decrypt($product->getAddress());
            $product->setAddress($decryptedAddress);
        }

        return $product;
    }

    public function encryptAllUnencryptedAddresses() {
        $repository = $this->entityManager->getRepository(Product::class);
        $products = $repository->findAll();
    
        foreach ($products as $product) {
            $address = $product->getAddress();
            
            // Assuming you have a method isEncrypted to check if the address is already encrypted
            if (!$this->isEncrypted($address)) {
                $encryptedAddress = $this->encryptionService->encrypt($address);
                $product->setAddress($encryptedAddress);
                $this->entityManager->persist($product);
            }
        }
        $this->entityManager->flush();
    }

    private function isEncrypted(string $string): bool {
        // Implement your logic to determine if a string is encrypted.
        // This is just a placeholder.
        return false;
    }
}
