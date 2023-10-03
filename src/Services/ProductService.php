<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use App\Product;

class ProductService {

    private $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function createProduct(string $name, string $address, bool $shouldEncrypt = true) {
        $product = new Product($name, $address, $shouldEncrypt);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $this->entityManager->clear();
    
        return $product->getId();
    }

    public function getProductById($id) {
        $productRepo = $this->entityManager->getRepository(Product::class);
        return $productRepo->find($id);
    }
}
