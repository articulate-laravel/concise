<?php
declare(strict_types=1);

namespace Articulate\Concise\Query;

use Articulate\Concise\Contracts\EntityMapper;
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
     * @noinspection PhpHierarchyChecksInspection
     */
    private function hydrate(array|object|null $record): ?object
    {
        if ($record === null) {
            return null;
        }

        return $this->mapper->toObject((array)$record);
    }

    /**
     * @param iterable<array<string, mixed>|\stdClass> $records
     *
     * @return \Illuminate\Support\Collection<array-key, EntityObject>
     */
    private function hydrateMany(iterable $records): Collection
    {
        $entities = [];

        foreach ($records as $record) {
            $entities[] = $this->hydrate((array)$record);
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
            return collect();
        }

        return $this->hydrateMany($records);
    }
}
