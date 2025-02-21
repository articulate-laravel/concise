<?php

namespace Articulate\Concise\Contracts;

use Articulate\Concise\Query\Builder;

/**
 *
 */
interface Criteria
{
    /**
     * @template EntityObject of object
     *
     * @param \Articulate\Concise\Query\Builder<EntityObject>          $query
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityObject> $mapper
     *
     * @return void
     */
    public function apply(Builder $query, EntityMapper $mapper): void;
}
