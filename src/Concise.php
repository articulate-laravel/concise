<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Contracts\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use InvalidArgumentException;

final class Concise
{
    /**
     * The Laravel application
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private Application $app;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    private DatabaseManager $databases;

    /**
     * @var \Articulate\Concise\IdentityMap
     */
    private IdentityMap $identities;

    /**
     * Entity mappers mapped by the entity class.
     *
     * @var array<class-string, \Articulate\Concise\Contracts\EntityMapper<*>>
     */
    private array $entityMappers = [];

    /**
     * Component mappers mapped by the component class.
     *
     * @var array<class-string, \Articulate\Concise\Contracts\Mapper<*>>
     */
    private array $componentMappers = [];

    /**
     * @var array<class-string, \Articulate\Concise\Contracts\Repository<*>>
     */
    private array $repositories = [];

    public function __construct(Application $app, DatabaseManager $databases)
    {
        $this->app        = $app;
        $this->identities = new IdentityMap();
        $this->databases  = $databases;
    }

    /**
     * Get all entity mappers
     *
     * @return array<class-string, \Articulate\Concise\Contracts\EntityMapper<*>>
     */
    public function getEntityMappers(): array
    {
        return $this->entityMappers;
    }

    /**
     * Get all component mappers
     *
     * @return array<class-string, \Articulate\Concise\Contracts\Mapper<*>>
     */
    public function getComponentMappers(): array
    {
        return $this->componentMappers;
    }

    /**
     * Registers the provided Mapper instance.
     *
     * @template ObjType of object
     *
     * @param class-string<\Articulate\Concise\Contracts\Mapper<ObjType>> $mapperClass The mapper class to register.
     *
     * @return self Returns the current instance for method chaining.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register(string $mapperClass): self
    {
        $mapper = $this->app->make($mapperClass);

        if ($mapper instanceof EntityMapper) {
            $this->entityMappers[$mapper->class()] = $mapper;
        } else {
            $this->componentMappers[$mapper->getClass()] = $mapper;
        }

        return $this;
    }

    /**
     * Creates an entity of the given class for the give data
     *
     * @template EntityType of object
     *
     * @param class-string<EntityType> $class The fully qualified class name of the entity.
     * @param array<string, mixed>     $data  The data to create the entity from.
     *
     * @return object The created entity
     *
     * @phpstan-return EntityType
     *
     * @throws InvalidArgumentException If no entity mapper is registered for the provided class.
     */
    public function entity(string $class, array $data): object
    {
        /** @var \Articulate\Concise\Contracts\EntityMapper<EntityType>|null $mapper */
        $mapper = $this->entityMappers[$class] ?? null;

        if ($mapper === null) {
            throw new InvalidArgumentException("No entity mapper registered for {$class}.");
        }

        return $this->identified($mapper, $data);
    }

    /**
     * Resolve and retrieve an identified entity
     *
     * @template EntityType of object
     *
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityType> $mapper
     * @param array<string, mixed>                                   $data
     *
     * @return object
     *
     * @phpstan-return EntityType
     */
    public function identified(EntityMapper $mapper, array $data): object
    {
        // Get the identity from the data
        $identity = $mapper->identity($data);

        // Retrieve an existing entity if one does exist
        $existing = $this->identities->get($mapper->class(), $identity);

        // If it does exist, return it instead
        if ($existing !== null) {
            return $existing;
        }

        // If it doesn't exist, create a new entity from the data
        $entity = $mapper->toObject($data);

        // And then map its identity
        $this->identities->add($entity, $identity, $mapper->class());

        return $entity;
    }

    /**
     * Create a component of the given class for the given data.
     *
     * @template ComponentType of object
     *
     * @param class-string<ComponentType> $class The fully qualified class name of the component.
     * @param array<string, mixed>        $data  The data to create the component from.
     *
     * @return object The created component
     *
     * @phpstan-return ComponentType
     *
     * @throws InvalidArgumentException If no component mapper is registered for the provided class.
     */
    public function component(string $class, array $data): object
    {
        /** @var \Articulate\Concise\Contracts\Mapper<ComponentType>|null $mapper */
        $mapper = $this->componentMappers[$class] ?? null;

        if ($mapper === null) {
            throw new InvalidArgumentException("No component mapper registered for {$class}.");
        }

        // Components don't have "identities", so we can return this

        return $mapper->toObject($data);
    }

    /**
     * Get the data representation for the given object.
     *
     * @template ObjType of object
     *
     * @param object          $object The object to convert into a data array
     *
     * @phpstan-param ObjType $object
     *
     * @return array<string, mixed> The data
     */
    public function data(object $object): array
    {
        /** @var \Articulate\Concise\Contracts\Mapper<ObjType>|null $mapper */
        $mapper = $this->entityMappers[$object::class] ?? $this->componentMappers[$object::class] ?? null;

        if ($mapper === null) {
            throw new InvalidArgumentException('No mapper registered for ' . $object::class . '.');
        }

        return $mapper->toData($object);
    }

    /**
     * Get a repository instance for the given entity class.
     *
     * @template EntityType of object
     *
     * @param class-string<EntityType> $class
     *
     * @return \Articulate\Concise\Contracts\Repository<EntityType>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function repository(string $class): Repository
    {
        $mapper = $this->entityMappers[$class] ?? null;

        if ($mapper === null) {
            throw new InvalidArgumentException('No mapper registered for ' . $class . '.');
        }

        $repository = $this->repositories[$class] ?? null;

        if ($repository !== null) {
            /** @phpstan-ignore return.type */
            return $repository;
        }

        $repositoryClass = $mapper->repository();

        /**
         * This has to be here because Application::make() expects a 'string',
         * not a 'class-string', which is...well, yeah, you know exactly what it
         * is.
         *
         * @phpstan-ignore argument.type
         */
        return $this->repositories[$class] = new $repositoryClass(
            concise   : $this,
            mapper    : $mapper,
            connection: $this->databases->connection($mapper->connection()),
        );
    }
}
