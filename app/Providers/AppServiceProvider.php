<?php

namespace App\Providers;

use Homeful\References\Models\Reference;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use App\Observers\ReferenceObserver;

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
        Vite::prefetch(concurrency: 3);
        Reference::observe(ReferenceObserver::class);
    }
}
