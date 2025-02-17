<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Illuminate\Support\ServiceProvider;

class ConciseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the Concise class
        $this->app->singleton(Concise::class, function () {
            return new Concise($this->app);
        });

        // Register the mappers' repositories
        $this->booted($this->registerRepositories(...));
    }

    protected function registerRepositories(Concise $concise): void
    {
        foreach ($concise->getEntityMappers() as $mapper) {
            $repo = $mapper->repository();

            if ($repo !== null) {
                $this->app->bind($repo, fn () => $concise->repository($mapper->getClass()));
            }
        }
    }
}
