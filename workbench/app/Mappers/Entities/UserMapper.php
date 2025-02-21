<?php
declare(strict_types=1);

namespace App\Mappers\Entities;

use App\Components\AuthCredentials;
use App\Components\Timestamps;
use App\Entities\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use OLD\Support\BaseEntityMapper;

/**
 * @extends OLD\Support\BaseEntityMapper<\App\Entities\User>
 */
final class UserMapper extends BaseEntityMapper
{
    /**
     * Get the object class.
     *
     * @return class-string<\App\Entities\User>
     */
    public function class(): string
    {
        return User::class;
    }

    /**
     * Get the repository class name.
     *
     * @return class-string<\App\Repositories\UserRepository>|null
     */
    public function repository(): ?string
    {
        return UserRepository::class;
    }

    /**
     * Converts the given array of data into an object.
     *
     * @param array<string, mixed> $data The input data to be converted into an object.
     *
     * @return object The resulting object created from the input data.
     *
     * @phpstan-return \App\Entities\User
     */
    public function toObject(array $data): object
    {
        $user = new User();

        $user->setId($data['id'])
             ->setName($data['name'])
             ->setAuth(
                 $this->concise->component(
                     AuthCredentials::class,
                     Arr::only($data, ['email', 'password', 'remember_token', 'email_verified_at'])
                 )
             )
             ->setTimestamps(
                 $this->concise->component(
                     Timestamps::class,
                     Arr::only($data, ['created_at', 'updated_at'])
                 )
             );

        return $user;
    }

    /**
     * Converts the given object into an associative array.
     *
     * @param object                     $object The object to be converted into an array.
     *
     * @phpstan-param \App\Entities\User $object
     *
     * @return array<string, mixed> The resulting associative array representation of the object.
     */
    public function toData(object $object): array
    {
        $data = [
            'name' => $object->getName(),
        ];

        if ($object->hasId()) {
            $data['id'] = $object->getId();
        }

        return array_merge(
            $this->concise->data($object->getAuth()),
            $this->concise->data($object->getTimestamps()),
            $data
        );
    }
}
