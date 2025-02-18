<?php

namespace Articulate\Concise\Contracts;

use SensitiveParameter;

/**
 * Authentication Repository
 *
 * This interface provides methods for retrieving and managing authentication-related entities,
 * such as finding users by their identifier, remember token, or credentials.
 *
 * @template EntityType of object
 *
 * @extends \Articulate\Concise\Contracts\Repository<EntityType>
 *
 * @see \Illuminate\Contracts\Auth\UserProvider
 */
interface AuthRepository extends Repository
{
    /**
     * Find an entity by its auth identifier.
     *
     * @param int $id
     *
     * @return object|null
     *
     * @phpstan-return EntityType|null
     *
     * @see \Illuminate\Contracts\Auth\UserProvider::retrieveById()
     */
    public function findByAuthIdentifier(int $id): ?object;

    /**
     * Find an entity by its remember token.
     *
     * @param string $token
     *
     * @return object|null
     *
     * @phpstan-return EntityType|null
     *
     * @see \Illuminate\Contracts\Auth\UserProvider::retrieveByToken()
     */
    public function findByRememberToken(#[SensitiveParameter] string $token): ?object;

    /**
     * Find an identity by its credentials
     *
     * @param array<string, mixed> $credentials
     *
     * @return object|null
     *
     * @phpstan-return EntityType|null
     *
     * @see \Illuminate\Contracts\Auth\UserProvider::retrieveByCredentials()
     */
    public function findByCredentials(#[SensitiveParameter] array $credentials): ?object;
}
