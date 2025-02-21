<?php
declare(strict_types=1);

namespace App\Providers;

use App\Mappers;
use OLD\Support\MapperServiceProvider as BaseMapperServiceProvider;

class MapperServiceProvider extends BaseMapperServiceProvider
{
    protected array $mappers = [
        // Components
        Mappers\Components\AuthCredentialsMapper::class,
        Mappers\Components\TimestampsMapper::class,

        // Entities
        Mappers\Entities\UserMapper::class,
    ];
}
