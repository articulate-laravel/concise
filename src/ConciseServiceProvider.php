<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Support\ImplicitBindingSubstitution;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ConciseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the main concise class
        $this->app->singleton(Concise::class);

        // Add our custom implicit binding substitution
        $this->app->afterResolving(Router::class, function (Router $router) {
            $router->substituteImplicitBindingsUsing($this->app->make(ImplicitBindingSubstitution::class));
        });
    }
}
