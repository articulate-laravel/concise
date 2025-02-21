<?php

namespace Articulate\Concise\Contracts;

/**
 * Mapper
 *
 * Mappers are responsible for mapping between raw data and object representations.
 *
 * @template MapObject of object
 */
interface Mapper
{
    /**
     * Get the class name this mapper represents.
     *
     * Returns the fully qualified class name of the object that this mapper is
     * mapping.
     *
     * @return class-string<MapObject>
     */
    public function class(): string;

    /**
     * Convert a raw array to an object representation.
     *
     * @param array<string, mixed> $data
     *
     * @return object
     *
     * @phpstan-return MapObject
     */
    public function toObject(array $data): object;

    /**
     * Convert an object representation to raw data.
     *
     * @param object            $object
     *
     * @phpstan-param MapObject $object
     *
     * @return array<string, mixed>
     */
    public function toData(object $object): array;
}
