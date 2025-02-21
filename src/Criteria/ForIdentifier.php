<?php
declare(strict_types=1);

namespace Articulate\Concise\Criteria;

use Articulate\Concise\Contracts\Criteria;
use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Query\Builder;

final readonly class ForIdentifier implements Criteria
{
    private string|int $identifier;

    public function __construct(string|int $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @template EntityObject of object
     *
     * @param \Articulate\Concise\Query\Builder<EntityObject>          $query
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityObject> $mapper
     *
     * @return void
     */
    public function apply(Builder $query, EntityMapper $mapper): void
    {
        $query->where(
            $query->qualifyColumn($mapper->identity()),
            '=',
            $this->identifier
        );
    }
}
