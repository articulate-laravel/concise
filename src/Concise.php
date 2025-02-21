<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Contracts\Mapper;
use Articulate\Concise\Contracts\Repository;
use Illuminate\Foundation\Application;

final class Concise
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    private Application $app;

    /**
     * @var array<class-string, \Articulate\Concise\Contracts\EntityMapper<*>>
     */
    private array $entityMappers = [];

    /**
     * @var array<class-string, \Articulate\Concise\Contracts\Mapper<*>>
     */
    private array $componentMappers = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a class mapper.
     *
     * @template MapperObject of object
     *
     * @param \Articulate\Concise\Contracts\Mapper<MapperObject> $mapper
     * @param bool                                               $overwrite
     *
     * @return bool
     */
    public function register(Mapper $mapper, bool $overwrite = false): bool
    {
        if ($mapper instanceof EntityMapper) {
            if ($overwrite === false && isset($this->entityMappers[$mapper->class()])) {
                return false;
            }

            $this->entityMappers[$mapper->class()] = $mapper;

            return true;
        }

        if ($overwrite === false && isset($this->componentMappers[$mapper->class()])) {
            return false;
        }

        $this->componentMappers[$mapper->class()] = $mapper;

        return true;
    }

    /**
     * Get a registered entity mapper for the provided class.
     *
     * @template EntityObject of object
     *
     * @param class-string<EntityObject> $class
     *
     * @return \Articulate\Concise\Contracts\EntityMapper<EntityObject>|null
     */
    public function entity(string $class): ?EntityMapper
    {
        /** @var \Articulate\Concise\Contracts\EntityMapper<EntityObject>|null $mapper */
        $mapper = $this->entityMappers[$class] ?? null;

        return $mapper;
    }

    /**
     * Get an entity repository for the provided class.
     *
     * @template EntityObject of object
     *
     * @param class-string<EntityObject> $class
     *
     * @return \Articulate\Concise\Contracts\Repository<EntityObject>|null
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function repository(string $class): ?Repository
    {
        /** @var \Articulate\Concise\Contracts\EntityMapper<EntityObject>|null $mapper */
        $mapper = $this->entity($class);

        if ($mapper === null) {
            return null;
        }

        $repository = $mapper->repository();

        if ($repository === null) {
            return new EntityRepository(
                $mapper,
                $this->app->make('db')->connection($mapper->connection()),
            );
        }

        return $this->app->make($repository, [
            'mapper'     => $mapper,
            'connection' => $this->app->make('db')->connection($mapper->connection()),
        ]);
    }
}
