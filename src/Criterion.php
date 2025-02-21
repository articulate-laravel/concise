<?php
declare(strict_types=1);

namespace Articulate\Concise;

use Articulate\Concise\Contracts\Criteria;
use Articulate\Concise\Criteria\ForIdentifier;
use Articulate\Concise\Criteria\WhereColumn;

final class Criterion
{
    public static function forIdentifier(int|string $identity): Criteria
    {
        return new ForIdentifier($identity);
    }

    public static function where(string $column, string $operation, mixed $value): Criteria
    {
        return new WhereColumn($column, $operation, $value);
    }

    public static function whereEqual(string $column, mixed $value): Criteria
    {
        return self::where($column, '=', $value);
    }

    public static function whereLessThan(string $column, mixed $value): Criteria
    {
        return self::where($column, '<', $value);
    }

    public static function whereGreaterThan(string $column, mixed $value): Criteria
    {
        return self::where($column, '>', $value);
    }

    public static function whereNull(string $column): Criteria
    {
        return self::where($column, 'IS', 'NULL');
    }
}
