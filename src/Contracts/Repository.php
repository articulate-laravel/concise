<?php

namespace Articulate\Concise\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * @template EntityObject of object
 */
interface Repository
{
    /**
     * Get one record for the provided criteria.
     *
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return object|null
     *
     * @phpstan-return EntityObject|null
     */
    public function getOne(Criteria ...$criteria): ?object;

    /**
     * Get many records for the provided criteria.
     *
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return \Illuminate\Support\Collection<array-key, EntityObject>
     */
    public function getMany(Criteria ...$criteria): Collection;

    /**
     * Get a paginated collection of records for the provided criteria.
     *
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return \Illuminate\Contracts\Pagination\Paginator<EntityObject>
     */
    public function getPaginated(Criteria ...$criteria): Paginator;
}
