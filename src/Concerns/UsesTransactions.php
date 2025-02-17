<?php
declare(strict_types=1);

namespace Articulate\Concise\Concerns;

use Closure;

/**
 * @phpstan-require-extends \Articulate\Concise\Support\BaseRepository
 *
 * @mixin \Articulate\Concise\Support\BaseRepository
 */
trait UsesTransactions
{
    /**
     * Executes a given callback within a database transaction.
     *
     * @template RetType of mixed
     *
     * @param Closure(\Illuminate\Database\Connection): RetType $callback The callback function to execute within the transaction.
     *
     * @return mixed The result of the callback function execution.
     *
     * @throws \Throwable
     */
    protected function inTransaction(Closure $callback): mixed
    {
        return $this->connection()->transaction($callback);
    }

    /**
     * Initiates a database transaction.
     *
     * @return void
     *
     * @throws \Throwable
     */
    protected function beginTransaction(): void
    {
        $this->connection()->beginTransaction();
    }

    /**
     * Finalizes a database transaction, committing all changes made during the transaction.
     *
     * @return void
     *
     * @throws \Throwable
     */
    protected function commitTransaction(): void
    {
        $this->connection()->commit();
    }

    /**
     * Rolls back the current database transaction, reverting any changes made
     * during the transaction.
     *
     * @return void
     * @throws \Throwable
     */
    protected function rollBackTransaction(): void
    {
        $this->connection()->rollBack();
    }
}
