<?php

namespace App\Application\Products\Controllers;

use App\Domain\Products\Services\Interfaces\ProductServiceInterface;
use App\Domain\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;

    /**
     * ProductController constructor.
     * 
     * @param ProductServiceInterface $productService
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductServiceInterface $productService,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
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
            'product_data' => array_merge($product->product_data, $data),
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

    /**
     * Create a new product
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
                'name' => 'required|string',
                'brands' => 'nullable|string',
                'quantity' => 'nullable|string',
            ]);
            
            // Check if product already exists
            $existingProduct = $this->productService->getProductByCode($request->input('code'));
            
            if ($existingProduct) {
                return response()->json([
                    'message' => 'Product with this code already exists',
                ], 409);
            }
            
            // Create product data
            $productData = [
                'product_name' => $request->input('name'),
                'brands' => $request->input('brands'),
                'quantity' => $request->input('quantity'),
                'ingredients_text' => $request->input('ingredients', ''),
                'nutriments' => $request->input('nutriments', []),
            ];
            
            // Create product
            $product = $this->productRepository->create([
                'code' => $request->input('code'),
                'status' => 'published',
                'product_data' => $productData,
                'imported_t' => now(),
            ]);
            
            return response()->json($product, 201);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating product: ' . $e->getMessage(),
            ], 500);
        }
    }
}