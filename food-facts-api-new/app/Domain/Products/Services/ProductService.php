<?php

namespace App\Domain\Products\Services;

use App\Domain\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Domain\Products\Services\Interfaces\ProductServiceInterface;
use App\Models\ImportHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductService implements ProductServiceInterface
{
    protected $productRepository;

    /**
     * ProductService constructor.
     * 
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get a product by its code
     * 
     * @param string $code
     * @return mixed
     */
    public function getProductByCode(string $code)
    {
        return $this->productRepository->findByCode($code);
    }

    /**
     * Get all products with pagination
     * 
     * @param int $perPage
     * @return mixed
     */
    public function getAllProducts(int $perPage = 10)
    {
        return $this->productRepository->getAll($perPage);
    }

    /**
     * Update a product by its code
     * 
     * @param string $code
     * @param array $data
     * @return mixed
     */
    public function updateProduct(string $code, array $data)
    {
        return $this->productRepository->updateByCode($code, $data);
    }

    /**
     * Delete a product by its code (set status to trash)
     * 
     * @param string $code
     * @return bool
     */
    public function deleteProduct(string $code)
    {
        return $this->productRepository->deleteByCode($code);
    }

    /**
     * Import products from Open Food Facts
     * 
     * @return array
     */
    public function importProducts()
    {
        try {
            // Get the index file
            $indexResponse = Http::get('https://challenges.coode.sh/food/data/json/index.txt');
            
            if (!$indexResponse->successful()) {
                throw new \Exception('Failed to fetch index file');
            }
            
            $files = explode("\n", $indexResponse->body());
            $files = array_filter($files);
            
            // Process only the first file for testing
            $file = $files[0];
            
            // Create import history record
            $importHistory = new ImportHistory();
            $importHistory->filename = $file;
            $importHistory->status = 'processing';
            $importHistory->imported_at = now();
            $importHistory->products_imported = 0;
            $importHistory->save();
            
            // Get the file content
            $fileUrl = "https://challenges.coode.sh/food/data/json/{$file}";
            $fileResponse = Http::get($fileUrl);
            
            if (!$fileResponse->successful()) {
                throw new \Exception("Failed to fetch file: {$file}");
            }
            
            $content = $fileResponse->body();
            $lines = explode("\n", $content);
            $lines = array_filter($lines);
            
            // Limit to 100 products
            $lines = array_slice($lines, 0, 100);
            
            $importedCount = 0;
            
            foreach ($lines as $line) {
                $productData = json_decode($line, true);
                
                if (!isset($productData['code'])) {
                    continue;
                }
                
                // Check if product already exists
                $existingProduct = $this->productRepository->findByCode($productData['code']);
                
                if ($existingProduct) {
                    // Update existing product
                    $this->productRepository->updateByCode($productData['code'], [
                        'product_data' => $productData,
                        'imported_t' => now(),
                    ]);
                } else {
                    // Create new product
                    $this->productRepository->create([
                        'code' => $productData['code'],
                        'status' => 'published',
                        'product_data' => $productData,
                        'imported_t' => now(),
                    ]);
                }
                
                $importedCount++;
            }
            
            // Update import history
            $importHistory->status = 'completed';
            $importHistory->products_imported = $importedCount;
            $importHistory->save();
            
            return [
                'success' => true,
                'message' => "Imported {$importedCount} products from {$file}",
            ];
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            
            if (isset($importHistory)) {
                $importHistory->status = 'failed';
                $importHistory->error_message = $e->getMessage();
                $importHistory->save();
            }
            
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ];
        }
    }
}