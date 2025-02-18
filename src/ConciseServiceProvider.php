<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class ConciseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the Concise class
        $this->app->singleton(Concise::class, function () {
            return new Concise(
                $this->app,
                $this->app->make(DatabaseManager::class),
            );
        });

        // Register the mappers' repositories
        $this->booted($this->registerRepositories(...));
    }

    /**
     * @param \Articulate\Concise\Concise $concise
     *
     * @return void
     */
    protected function registerRepositories(Concise $concise): void
    {
        /**
         * @var class-string $entity
         * @var \Articulate\Concise\Contracts\EntityMapper<*> $mapper
         */
        foreach ($concise->getEntityMappers() as $entity => $mapper) {
            $repo = $mapper->repository();

            if ($repo !== null) {
                $this->app->bind($repo, fn () => $concise->repository($entity));
            }
        }
    }
}
