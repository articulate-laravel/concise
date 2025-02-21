<?php
declare(strict_types=1);

namespace Articulate\Concise\Criteria;

use Articulate\Concise\Contracts\Criteria;
use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Query\Builder;

final class WhereColumn implements Criteria
{
    private string $column;

    private string $operator;

    private mixed  $value;

    public function __construct(string $column, string $operator, mixed $value)
    {
        $this->column   = $column;
        $this->operator = $operator;
        $this->value    = $value;
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
        $query->where($this->column, $this->operator, $this->value);
    }
}
