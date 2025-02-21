<?php
declare(strict_types=1);

namespace App\Repositories;

use OLD\Concerns\SupportsAuth;
use OLD\Concerns\UsesTransactions;
use OLD\Contracts\AuthRepository;
use OLD\Support\BaseRepository;

/**
 * @extends OLD\Support\BaseRepository<\App\Entities\User>
 */
final class UserRepository extends BaseRepository implements AuthRepository
{
    use UsesTransactions,
        SupportsAuth;
}
