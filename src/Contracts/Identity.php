<?php

namespace Articulate\Concise\Contracts;

use Stringable;

interface Identity extends Stringable
{
    public function toKey(): string|int;
}
