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

        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        // NOTE: ProductAttributeValueObserver removed — Eloquent upsert() bypasses
        // model events, so the observer was never called. Flat sync is handled
        // directly by ProductRepository::syncToFlat() after bulk upsert.
    }
}
