<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        // NOTE: ProductObserver and ProductAttributeValueObserver have been removed.
        // Flat sync is handled synchronously inside ProductRepository via syncToFlat()
        // to prevent partial updates / race conditions.
    }
}
