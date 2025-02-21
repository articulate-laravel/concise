<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Contracts\Criteria;
use Articulate\Concise\Contracts\EntityMapper;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

/**
 * @template EntityObject of object
 *
 * @implements \Articulate\Concise\Contracts\Repository<EntityObject>
 * @implements \Articulate\Concise\Contracts\RoutableRepository<EntityObject>
 */
final class EntityRepository implements Contracts\Repository, Contracts\RoutableRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private Connection $connection;

    /**
     * @var \Articulate\Concise\Contracts\EntityMapper<EntityObject>
     */
    private EntityMapper $mapper;

    /**
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityObject> $mapper
     */
    public function __construct(EntityMapper $mapper, Connection $connection)
    {
        $this->mapper     = $mapper;
        $this->connection = $connection;
    }

    /**
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return \Articulate\Concise\Query\Builder<EntityObject>
     */
    private function query(Criteria ...$criteria): Query\Builder
    {
        $query = new Query\Builder(
            $this->mapper,
            $this->connection,
        );

        $query->from($this->mapper->table());

        foreach ($criteria as $criterion) {
            $criterion->apply($query, $this->mapper);
        }

        return $query;
    }

    /**
     * Get one record for the provided criteria.
     *
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return object|null
     *
     * @phpstan-return EntityObject|null
     */
    public function getOne(Criteria ...$criteria): ?object
    {
        return $this->query(...$criteria)->first();
    }

    /**
     * Get many records for the provided criteria.
     *
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return \Illuminate\Support\Collection<array-key, EntityObject>
     */
    public function getMany(Criteria ...$criteria): Collection
    {
        return $this->query(...$criteria)->get();
    }

    /**
     * Get a paginated collection of records for the provided criteria.
     *
     * @param \Articulate\Concise\Contracts\Criteria ...$criteria
     *
     * @return \Illuminate\Contracts\Pagination\Paginator<EntityObject>
     */
    public function getPaginated(Criteria ...$criteria): Paginator
    {
        return $this->query(...$criteria)->paginate();
    }

    /**
     * @param string      $value
     * @param string|null $binding
     *
     * @return object|null
     *
     * @phpstan-return EntityObject|null
     */
    public function getOneForRouting(string $value, ?string $binding = null): ?object
    {
        if ($binding === null) {
            return $this->getOne(Criterion::forIdentifier($value));
        }

        return $this->getOne(Criterion::where($binding, '=', $value));
    }
}
