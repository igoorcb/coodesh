<?php

namespace App\Domain\Products\Services\Interfaces;

interface ProductServiceInterface
{
    public function getProductByCode(string $code);
    public function getAllProducts(int $perPage = 10);
    public function updateProduct(string $code, array $data);
    public function deleteProduct(string $code);
    public function importProducts();
}