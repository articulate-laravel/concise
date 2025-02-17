<?php

namespace Articulate\Concise\Contracts;


/**
 * Mapper
 *
 * Defines a mapping interface for converting between data arrays and objects.
 *
 * @template ObjType of object
 */
interface Mapper
{
    /**
     * Get the object class.
     *
     * @return class-string<ObjType>
     */
    public function getClass(): string;

    /**
     * Converts the given array of data into an object.
     *
     * @param array<string, mixed> $data The input data to be converted into an object.
     *
     * @return object The resulting object created from the input data.
     *
     * @phpstan-return ObjType
     */
    public function toObject(array $data): object;

    /**
     * Converts the given object into an associative array.
     *
     * @param object          $object The object to be converted into an array.
     *
     * @phpstan-param ObjType $object
     *
     * @return array<string, mixed> The resulting associative array representation of the object.
     */
    public function toData(object $object): array;
}
