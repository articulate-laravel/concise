<?php

namespace Articulate\Concise\Contracts;

/**
 * Entity Mapper
 *
 * Entity mappers are more specific implementations of
 * {@see \Articulate\Concise\Contracts\Mapper} that deal with entities stored in
 * the database.
 *
 * @template EntityObject of object
 *
 * @extends  \Articulate\Concise\Contracts\Mapper<EntityObject>
 */
interface EntityMapper extends Mapper
{
    /**
     * Get the custom repository class for the entity
     *
     * @return class-string<\Articulate\Concise\Contracts\Repository<EntityObject>>|null
     */
    public function repository(): ?string;

    /**
     * Get the connection the entity should use
     *
     * @return string|null
     */
    public function connection(): ?string;

    /**
     * Get the entities' database table
     *
     * @return string
     */
    public function table(): string;

    /**
     * Get the name of the identity field
     *
     * @return string
     */
    public function identity(): string;
}
