<?php

namespace App\Application\Products\Controllers;

use App\Domain\Products\Services\Interfaces\ProductServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productService;

    /**
     * ProductController constructor.
     * 
     * @param ProductServiceInterface $productService
     */
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display API information
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get last CRON execution time
        $lastImport = \App\Models\ImportHistory::orderBy('imported_at', 'desc')->first();
        $lastCronTime = $lastImport ? $lastImport->imported_at : 'Never';
        
        // Get server uptime
        $uptime = shell_exec('uptime -p');
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $dbConnection = true;
        } catch (\Exception $e) {
            $dbConnection = false;
        }
        
        // Get memory usage
        $memoryUsage = round(memory_get_usage() / 1024 / 1024, 2) . ' MB';
        
        return response()->json([
            'name' => 'Food Facts API',
            'version' => '1.0.0',
            'database_connection' => $dbConnection ? 'OK' : 'Failed',
            'last_cron_execution' => $lastCronTime,
            'uptime' => $uptime,
            'memory_usage' => $memoryUsage,
        ]);
    }

    /**
     * List all products with pagination
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listProducts(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $products = $this->productService->getAllProducts($perPage);
        
        return response()->json($products);
    }

    /**
     * Get a specific product by code
     * 
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProduct(string $code)
    {
        $product = $this->productService->getProductByCode($code);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }

    /**
     * Update a product
     * 
     * @param Request $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(Request $request, string $code)
    {
        $product = $this->productService->getProductByCode($code);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        $data = $request->all();
        $updatedProduct = $this->productService->updateProduct($code, [
            'product_data' => json_encode(array_merge(json_decode($product->product_data, true), $data)),
        ]);
        
        return response()->json($updatedProduct);
    }

    /**
     * Delete a product (set status to trash)
     * 
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(string $code)
    {
        $result = $this->productService->deleteProduct($code);
        
        if (!$result) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json(['message' => 'Product moved to trash']);
    }

    /**
     * Manually trigger product import
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function importProducts()
    {
        $result = $this->productService->importProducts();
        
        return response()->json($result);
    }
}