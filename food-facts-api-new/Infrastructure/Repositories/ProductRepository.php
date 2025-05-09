<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Products\Models\Product;
use App\Domain\Products\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function findByCode(string $code)
    {
        return $this->model->where('code', $code)->first();
    }

    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function updateByCode(string $code, array $data)
    {
        $product = $this->findByCode($code);
        
        if (!$product) {
            return null;
        }
        
        $product->update($data);
        return $product;
    }

    public function deleteByCode(string $code)
    {
        $product = $this->findByCode($code);
        
        if (!$product) {
            return false;
        }
        
        $product->status = 'trash';
        $product->save();
        
        return true;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}