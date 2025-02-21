<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Illuminate\Support\Str;

/**
 * @template EntityObject of object
 *
 * @implements \Articulate\Concise\Contracts\EntityMapper<EntityObject>
 */
abstract class EntityMapper implements Contracts\EntityMapper
{
    /**
     * @var \Articulate\Concise\Concise
     */
    protected Concise $concise;

    public function __construct(Concise $concise)
    {
        $this->concise = $concise;
    }

    /**
     * Get the custom repository class for the entity
     *
     * @return null
     */
    public function repository(): null
    {
        return null;
    }

    /**
     * Get the connection the entity should use
     *
     * @return null
     */
    public function connection(): null
    {
        return null;
    }

    /**
     * Get the entities' database table
     *
     * @return string
     */
    public function table(): string
    {
        return Str::snake(Str::pluralStudly(class_basename($this->class())));
    }

    /**
     * Get the name of the identity field
     *
     * @return string
     */
    public function identity(): string
    {
        return 'id';
    }

}
