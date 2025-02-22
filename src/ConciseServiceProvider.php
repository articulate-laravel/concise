<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Commands\MakeEntityCommand;
use Articulate\Concise\Commands\MakeMapperCommand;
use Articulate\Concise\Support\ImplicitBindingSubstitution;
use Articulate\Concise\Support\MapperDiscovery;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Events\PublishingStubs;
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

        $this->app->booted(function (Application $app) {
            $mappers = MapperDiscovery::discover();
            $concise = $app->make(Concise::class);

            foreach ($mappers as $mapper) {
                $concise->register($mapper);
            }
        });

        $this->app->afterResolving(Dispatcher::class, function (Dispatcher $dispatcher) {
            $dispatcher->listen(PublishingStubs::class, function (PublishingStubs $event) {
                $event->add(__DIR__ . '/../resources/stubs/entity.stub', 'entity.stub');
                $event->add(__DIR__ . '/../resources/stubs/mapper.entity.connection.stub', 'entity.stub');
                $event->add(__DIR__ . '/../resources/stubs/mapper.entity.identity.stub', 'entity.stub');
                $event->add(__DIR__ . '/../resources/stubs/mapper.entity.stub', 'entity.stub');
                $event->add(__DIR__ . '/../resources/stubs/mapper.entity.table.stub', 'entity.stub');
            });
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeMapperCommand::class,
            MakeEntityCommand::class,
        ]);
    }
}
