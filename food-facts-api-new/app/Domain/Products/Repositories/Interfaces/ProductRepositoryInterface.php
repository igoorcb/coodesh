<?php

namespace App\Domain\Products\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    public function findByCode(string $code);
    public function getAll(int $perPage = 10);
    public function updateByCode(string $code, array $data);
    public function deleteByCode(string $code);
    public function create(array $data);
}