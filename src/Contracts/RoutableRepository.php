<?php

namespace Articulate\Concise\Contracts;

/**
 * @template EntityObject of object
 *
 * @requires \Articulate\Concise\Contracts\Repository<EntityObject>
 */
interface RoutableRepository
{
    /**
     * @param string      $value
     * @param string|null $binding
     *
     * @return object|null
     *
     * @phpstan-return EntityObject|null
     */
    public function getOneForRouting(string $value, ?string $binding = null): ?object;
}
