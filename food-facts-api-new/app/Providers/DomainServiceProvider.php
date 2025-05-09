<?php

namespace App\Providers;

use App\Domain\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Domain\Products\Services\Interfaces\ProductServiceInterface;
use App\Domain\Products\Services\ProductService;
use App\Infrastructure\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind repositories
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // Bind services
        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}