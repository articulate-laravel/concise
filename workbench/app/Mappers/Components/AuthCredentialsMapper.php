<?php
declare(strict_types=1);

namespace App\Mappers\Components;

use App\Components\AuthCredentials;
use Articulate\Concise\Contracts\Mapper;

/**
 * @implements \Articulate\Concise\Contracts\Mapper<\App\Components\AuthCredentials>
 */
final class AuthCredentialsMapper implements Mapper
{
    /**
     * Get the object class.
     *
     * @return class-string<\App\Components\AuthCredentials>
     */
    public function getClass(): string
    {
        return AuthCredentials::class;
    }

    /**
     * Converts the given array of data into an object.
     *
     * @param array<string, mixed> $data The input data to be converted into an object.
     *
     * @return object The resulting object created from the input data.
     *
     * @phpstan-return \App\Components\AuthCredentials
     */
    public function toObject(array $data): object
    {
        return new AuthCredentials(
            $data['email'],
            $data['password'],
            $data['remember_token'] ?? null,
            $data['email_verified_at'] ?? null,
        );
    }

    /**
     * Converts the given object into an associative array.
     *
     * @param object                                  $object The object to be converted into an array.
     *
     * @phpstan-param \App\Components\AuthCredentials $object
     *
     * @return array<string, mixed> The resulting associative array representation of the object.
     */
    public function toData(object $object): array
    {
        assert($object instanceof AuthCredentials, 'Object must be an instance of ' . AuthCredentials::class);

        return [
            'email'             => $object->getEmail(),
            'password'          => $object->getPassword(),
            'remember_token'    => $object->getRememberToken(),
            'email_verified_at' => $object->getEmailVerifiedAt(),
        ];
    }
}
