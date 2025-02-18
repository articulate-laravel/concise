<?php

namespace Articulate\Concise\Contracts;

use Articulate\Concise\Concise;
use Illuminate\Database\Connection;

/**
 * @template EntityType of object
 */
interface Repository
{
    /**
     * Create a new repository instance.
     *
     * @param \Articulate\Concise\Concise                            $concise
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityType> $mapper
     * @param \Illuminate\Database\Connection                        $connection
     */
    public function __construct(
        Concise      $concise,
        EntityMapper $mapper,
        Connection   $connection
    );
}
