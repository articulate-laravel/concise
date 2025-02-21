<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Contracts\Mapper;
use Articulate\Concise\Contracts\Repository;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionClass;
use RuntimeException;
use Throwable;

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
     * @return \Articulate\Concise\Contracts\Repository<EntityObject>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function repository(string $class): Repository
    {
        /** @var \Articulate\Concise\Contracts\EntityMapper<EntityObject>|null $mapper */
        $mapper = $this->entity($class);

        if ($mapper === null) {
            throw new RuntimeException('No entity mapper registered for class [' . $class . ']');
        }

        $repository = $mapper->repository();

        if ($repository === null) {
            return new EntityRepository(
                $mapper,
                $this->app->make('db')->connection($mapper->connection()),
            );
        }

        /** @var \Articulate\Concise\Contracts\Repository<EntityObject> $instance */
        $instance = $this->app->make($repository, [
            'mapper'     => $mapper,
            'connection' => $this->app->make('db')->connection($mapper->connection()),
        ]);

        return $instance;
    }

    /**
     * Get a lazy entity instance.
     *
     * @template EntityObject of object
     *
     * @param class-string<EntityObject> $class
     * @param string|int                 $identity
     * @param array<string, mixed>       $data
     *
     * @return object|null
     *
     * @phpstan-return EntityObject|null
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function lazy(string $class, string|int $identity, array $data = []): ?object
    {
        /** @var \Articulate\Concise\Contracts\EntityMapper<EntityObject>|null $mapper */
        $mapper = $this->entity($class);

        if ($mapper === null) {
            return null;
        }

        $repository = $this->repository($class);

        try {
            $reflector = new ReflectionClass($class);
            $lazy      = $reflector->newLazyProxy(
            /**
             * @phpstan-param EntityObject $proxy
             */
                function (object $proxy) use ($repository, $identity, $mapper) {
                    /** @var EntityObject|null $entity */
                    $entity = $repository->getOne(Criterion::forIdentifier($identity));

                    if ($entity === null) {
                        throw new RecordsNotFoundException('No results for entity [' . $mapper->class() . ']');
                    }

                    return $entity;
                }
            );
        } catch (Throwable $e) {
            report($e);

            return null;
        }

        foreach ($data as $property => $value) {
            if ($reflector->hasProperty($property)) {
                $reflector->getProperty($property)->setRawValueWithoutLazyInitialization($lazy, $value);
            } else {
                $property = Str::studly($property);

                if ($reflector->hasProperty($property)) {
                    $reflector->getProperty($property)->setRawValueWithoutLazyInitialization($lazy, $value);
                }
            }
        }

        return $lazy;
    }
}
