<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Concise;
use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Contracts\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * @template EntityType of object
 *
 * @implements \Articulate\Concise\Contracts\Repository<EntityType>
 */
abstract class BaseRepository implements Repository
{
    /**
     * @var \Articulate\Concise\Concise
     */
    private Concise $concise;

    /**
     * @var \Articulate\Concise\Contracts\EntityMapper<EntityType>
     */
    private EntityMapper $mapper;

    /**
     * @var \Illuminate\Database\Connection
     */
    private Connection $connection;

    /**
     * @param \Articulate\Concise\Concise                            $concise
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityType> $mapper
     * @param \Illuminate\Database\Connection                        $connection
     */
    public function __construct(
        Concise      $concise,
        EntityMapper $mapper,
        Connection   $connection
    )
    {
        $this->concise    = $concise;
        $this->mapper     = $mapper;
        $this->connection = $connection;
    }

    /**
     * @return \Articulate\Concise\Concise
     */
    protected function concise(): Concise
    {
        return $this->concise;
    }

    /**
     * @return \Articulate\Concise\Contracts\EntityMapper<EntityType>
     */
    protected function mapper(): EntityMapper
    {
        return $this->mapper;
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    protected function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query(): Builder
    {
        return $this->connection()->query();
    }

    /**
     * Hydrate a single entity from data
     *
     * @param array<string, mixed>|null $data
     *
     * @return object|null
     *
     * @phpstan-return EntityType
     */
    protected function hydrate(?array $data): ?object
    {
        if ($data === null) {
            return null;
        }

        return $this->concise()->identified($this->mapper(), $data);
    }

    /**
     * Hydrate a collection of entities from data
     *
     * @param \Illuminate\Support\Collection<int, array<string, mixed>> $collection
     *
     * @return \Illuminate\Support\Collection<int, EntityType>
     */
    protected function hydrateMany(Collection $collection): Collection
    {
        $newCollection = new Collection();

        foreach ($collection as $data) {
            $entity = $this->hydrate($data);

            if ($entity !== null) {
                $newCollection->push($entity);
            }
        }

        return $newCollection;
    }
}
