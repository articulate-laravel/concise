<?php

namespace App\Providers;

use Articulate\Concise\Support\MapperDiscovery;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        MapperDiscovery::noDefaults();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
