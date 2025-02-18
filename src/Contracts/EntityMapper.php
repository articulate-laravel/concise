<?php

namespace Articulate\Concise\Contracts;

/**
 * EntityMapper
 *
 * Defines an interface for mapping entities.
 *
 * @template EntityType of object
 *
 * @extends \Articulate\Concise\Contracts\Mapper<EntityType>
 */
interface EntityMapper extends Mapper
{
    /**
     * Get the connection name the entity uses.
     *
     * @return string|null The connection name or null to use the default.
     */
    public function connection(): ?string;

    /**
     * Get the repository class name.
     *
     * @return class-string<\Articulate\Concise\Contracts\Repository<EntityType>>|null
     */
    public function repository(): ?string;

    /**
     * Get the entity table name.
     *
     * @return string
     */
    public function table(): string;

    /**
     * Get the identity from the provided data.
     *
     * @param array<string, mixed>|object             $data
     *
     * @phpstan-param array<string, mixed>|EntityType $data
     *
     * @return int|string|null
     */
    public function identity(array|object $data): int|string|null;
}
