<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\User;
use Articulate\Concise\Concerns\UsesTransactions;
use Articulate\Concise\Support\BaseRepository;
use SensitiveParameter;

/**
 * @extends \Articulate\Concise\Support\BaseRepository<\App\Entities\User>
 */
final class UserRepository extends BaseRepository
{
    use UsesTransactions;

    public function findById(int $id): ?User
    {
        return $this->hydrate(
            $this->query()->where('id', $id)->first()
        );
    }

    public function findByToken(#[SensitiveParameter] string $token): ?User
    {
        return $this->hydrate(
            $this->query()->where('remember_token', $token)->first()
        );
    }
}
