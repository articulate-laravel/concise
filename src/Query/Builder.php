<?php
declare(strict_types=1);

namespace Articulate\Concise\Query;

use Articulate\Concise\Contracts\EntityMapper;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Collection;

/**
 * @template EntityObject of object
 */
class Builder extends BaseBuilder
{
    /** @use \Illuminate\Database\Concerns\BuildsQueries<EntityObject> */
    use BuildsQueries;

    /**
     * @var \Articulate\Concise\Contracts\EntityMapper<EntityObject>
     */
    private EntityMapper $mapper;

    /**
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityObject> $mapper
     * @param \Illuminate\Database\ConnectionInterface                 $connection
     * @param \Illuminate\Database\Query\Grammars\Grammar|null         $grammar
     * @param \Illuminate\Database\Query\Processors\Processor|null     $processor
     */
    public function __construct(
        EntityMapper        $mapper,
        ConnectionInterface $connection,
        ?Grammar            $grammar = null,
        ?Processor          $processor = null
    )
    {
        $this->mapper = $mapper;

        parent::__construct($connection, $grammar, $processor);
    }

    /**
     * @param array<string, mixed>|object|null $record
     *
     * @return object|null
     *
     * @phpstan-return EntityObject|null
     */
    private function hydrate(array|object|null $record): ?object
    {
        if ($record === null) {
            return null;
        }

        if (is_object($record)) {
            /** @var array<string, mixed> $record */
            $record = (array)$record;
        }

        return $this->mapper->toObject($record);
    }

    /**
     * @param iterable<array<string, mixed>|object> $records
     *
     * @return \Illuminate\Support\Collection<array-key, EntityObject>
     */
    private function hydrateMany(iterable $records): Collection
    {
        $entities = [];

        foreach ($records as $record) {
            $entities[] = $this->hydrate($record);
        }

        return collect(array_filter($entities));
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array<string>|string $columns
     *
     * @return \Illuminate\Support\Collection<int, EntityObject>
     *
     * @phpstan-ignore method.childReturnType
     */
    public function get($columns = ['*']): Collection
    {
        $records = parent::get($columns);

        if ($records->isEmpty()) {
            /** @var \Illuminate\Support\Collection<int, EntityObject> */
            return collect();
        }

        return $this->hydrateMany($records);
    }

    /**
     * Qualify the given column name by the entity table.
     *
     * @param string|\Illuminate\Contracts\Database\Query\Expression $column
     *
     * @return string
     */
    public function qualifyColumn(string|Expression $column): string
    {
        $column = $column instanceof Expression ? (string)$column->getValue($this->getGrammar()) : $column;
        $table  = $this->mapper->table();

        if (str_contains($column, '.')) {
            return $column;
        }

        return $table . '.' . $column;
    }
}
