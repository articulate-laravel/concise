<?php
declare(strict_types=1);

namespace Articulate\Concise;

final class IdentityMap
{
    /**
     * @var array<class-string, array<string|int, object>>
     */
    private array $entities = [];

    /**
     * Adds an entity to the collection with the specified identity and class.
     *
     * @template EntityType of object
     *
     * @param object                        $entity   The entity to be added.
     * @param string|int                    $identity The unique identifier for the entity.
     * @param class-string<EntityType>|null $class    The class name or null to use the entity's class.
     *
     * @phpstan-param EntityType            $entity
     *
     * @return self Returns the current instance for method chaining.
     */
    public function add(object $entity, string|int $identity, ?string $class = null): self
    {
        $this->entities[$class ?? $entity::class][$identity] = $entity;

        return $this;
    }

    /**
     * Retrieves an entity object based on the provided class and identity.
     *
     * @template EntityType of object
     *
     * @param class-string<EntityType> $class    The class name used to identify the entity type.
     * @param string|int               $identity The identity key of the specific entity to retrieve.
     *
     * @return object|null Returns the entity object if found, or null if not found.
     *
     * @phpstan-return EntityType|null
     */
    public function get(string $class, string|int $identity): ?object
    {
        return $this->entities[$class][$identity] ?? null; /** @phpstan-ignore return.type */
    }

    /**
     * Check if a mapping exists for the given class and identity.
     *
     * @template EntityType of object
     *
     * @param class-string<EntityType> $class    The class name used to identify the entity type.
     * @param string|int               $identity The identity key of the specific entity to check.
     *
     * @return bool Returns true if an entity is mapped to the identity, or false if not
     */
    public function has(string $class, string|int $identity): bool
    {
        return isset($this->entities[$class][$identity]);
    }
}
