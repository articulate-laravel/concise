<?php
declare(strict_types=1);

namespace Articulate\Concise\Concerns;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use SensitiveParameter;

/**
 * @template EntityType of object
 *
 * @phpstan-require-implements \Articulate\Concise\Contracts\AuthRepository
 * @phpstan-require-extends \Articulate\Concise\Support\BaseRepository
 *
 * @mixin \Articulate\Concise\Support\BaseRepository<EntityType>
 */
trait SupportsAuth
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     * @phpstan-var EntityType
     */
    private Authenticatable $entity;

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable
     *
     * @phpstan-return EntityType&\Illuminate\Contracts\Auth\Authenticatable
     */
    private function entity(): Authenticatable
    {
        if (! isset($this->entity)) {
            $entityClass  = $this->mapper()->class();
            $this->entity = new $entityClass();
        }

        return $this->entity;
    }

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
    public function findByAuthIdentifier(int $id): ?object
    {
        return $this->hydrate(
            $this->query()->where($this->entity()->getAuthIdentifierName(), $id)->first()
        );
    }

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
    public function findByRememberToken(#[SensitiveParameter] string $token): ?object
    {
        return $this->hydrate(
            $this->query()->where($this->entity()->getRememberTokenName(), $token)->first()
        );
    }

    /**
     * Find an identity by its credentials
     *
     * @param array $credentials
     *
     * @return object|null
     *
     * @phpstan-return EntityType|null
     *
     * @see \Illuminate\Contracts\Auth\UserProvider::retrieveByCredentials()
     */
    public function findByCredentials(#[SensitiveParameter] array $credentials): ?object
    {
        $credentials = array_filter(
            $credentials,
            fn ($key) => ! str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = $this->query();

        foreach ($credentials as $key => $value) {
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
            } else {
                $query->where($key, $value);
            }
        }

        // Now we are ready to execute the query to see if we have a user matching
        // the given credentials. If not, we will just return null and indicate
        // that there are no matching users from the given credential arrays.
        return $this->hydrate($query->first());
    }
}
