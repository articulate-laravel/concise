<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Concise;
use Articulate\Concise\Contracts\EntityMapper;
use RuntimeException;
use Stringable;

/**
 * @template EntityType of object
 *
 * @implements \Articulate\Concise\Contracts\EntityMapper<EntityType>
 */
abstract class BaseEntityMapper implements EntityMapper
{
    /**
     * @var \Articulate\Concise\Concise
     */
    protected Concise $concise;

    public function __construct(Concise $concise)
    {
        $this->concise = $concise;
    }

    /**
     * Get the connection name the entity uses.
     *
     * @return string|null The connection name or null to use the default.
     */
    public function connection(): ?string
    {
        return null;
    }

    /**
     * Get the repository class name.
     *
     * @return class-string<\Articulate\Concise\Contracts\Repository<EntityType>>|null
     */
    public function repository(): ?string
    {
        return null;
    }

    /**
     * Get the identity from the provided data.
     *
     * @param array<string, mixed>|object             $data
     *
     * @phpstan-param array<string, mixed>|EntityType $data
     *
     * @return int|string
     */
    public function identity(object|array $data): int|string
    {
        if (is_object($data)) {
            if (method_exists($data, 'hasId') && ! $data->hasId()) {
                throw new RuntimeException('Cannot get identity from an entity without an ID');
            }

            if (property_exists($data, 'id')) {
                return $data->id;
            }

            if (method_exists($data, 'getId')) {
                return $data->getId();
            }

            if ($data instanceof Stringable) {
                return (string)$data;
            }

            throw new RuntimeException('Cannot get identity from an entity ' . $data::class);
        }

        if (! isset($data['id'])) {
            throw new RuntimeException('Cannot get identity from an entity without an ID');
        }

        assert(is_string($data['id']) || is_int($data['id']), 'The ID must be an int or string');

        return $data['id'];
    }
}
