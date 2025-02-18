<?php
declare(strict_types=1);

namespace App\Mappers\Components;

use App\Components\Timestamps;
use Articulate\Concise\Contracts\Mapper;

/**
 * @implements \Articulate\Concise\Contracts\Mapper<\App\Components\Timestamps>
 */
final class TimestampsMapper implements Mapper
{
    /**
     * Get the object class.
     *
     * @return class-string<\App\Components\Timestamps>
     */
    public function class(): string
    {
        return Timestamps::class;
    }

    /**
     * Converts the given array of data into an object.
     *
     * @param array<string, mixed> $data The input data to be converted into an object.
     *
     * @return object The resulting object created from the input data.
     *
     * @phpstan-return \App\Components\Timestamps
     */
    public function toObject(array $data): object
    {
        return new Timestamps(
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }

    /**
     * Converts the given object into an associative array.
     *
     * @param object                             $object The object to be converted into an array.
     *
     * @phpstan-param \App\Components\Timestamps $object
     *
     * @return array<string, mixed> The resulting associative array representation of the object.
     */
    public function toData(object $object): array
    {
        return [
            'created_at' => $object->getCreatedAt(),
            'updated_at' => $object->getUpdatedAt(),
        ];
    }
}
