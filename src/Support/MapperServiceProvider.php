<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Concise;
use Illuminate\Support\ServiceProvider;

abstract class MapperServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string<\Articulate\Concise\Contracts\EntityMapper<*>>>
     */
    protected array $mappers = [];

    public function register(): void
    {
        $this->app->afterResolving(Concise::class, function (Concise $concise) {
            foreach ($this->mappers as $mapper) {
                $concise->register($mapper);
            }
        });
    }
}
