<?php
declare(strict_types=1);

namespace App\Repositories;

use Articulate\Concise\Concerns\SupportsAuth;
use Articulate\Concise\Concerns\UsesTransactions;
use Articulate\Concise\Contracts\AuthRepository;
use Articulate\Concise\Support\BaseRepository;

/**
 * @extends \Articulate\Concise\Support\BaseRepository<\App\Entities\User>
 */
final class UserRepository extends BaseRepository implements AuthRepository
{
    use UsesTransactions,
        SupportsAuth;
}
