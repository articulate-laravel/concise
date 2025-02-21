<?php
declare(strict_types=1);

namespace Articulate\Concise\Tests;

use App\Components\AuthCredentials;
use App\Components\Timestamps;
use App\Entities\User;
use App\Mappers\Components\AuthCredentialsMapper;
use App\Mappers\Components\TimestampsMapper;
use App\Mappers\Entities\UserMapper;
use Articulate\Concise\Concise;
use Articulate\Concise\EntityRepository;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use stdClass;

class ConciseTest extends TestCase
{
    #[Test]
    public function canManuallyRegisterEntityMappers(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(new UserMapper($concise)));
        $this->assertInstanceOf(UserMapper::class, $concise->entity(User::class));
    }

    #[Test]
    public function canManuallyRegisterComponentMappers(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(new AuthCredentialsMapper()));
        $this->assertTrue($concise->register(new TimestampsMapper()));
        $this->assertInstanceOf(AuthCredentialsMapper::class, $concise->component(AuthCredentials::class));
        $this->assertInstanceOf(TimestampsMapper::class, $concise->component(Timestamps::class));
    }

    #[Test]
    public function canManuallyRegisterEntityMappersByClass(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(UserMapper::class));
        $this->assertInstanceOf(UserMapper::class, $concise->entity(User::class));
    }

    #[Test]
    public function canManuallyRegisterComponentMappersByClass(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(AuthCredentialsMapper::class));
        $this->assertTrue($concise->register(TimestampsMapper::class));
        $this->assertInstanceOf(AuthCredentialsMapper::class, $concise->component(AuthCredentials::class));
        $this->assertInstanceOf(TimestampsMapper::class, $concise->component(Timestamps::class));
    }

    #[Test]
    public function throwsAnExceptionIfTheClassIsNotAMapper(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class [stdClass] is not a valid mapper.');

        $concise->register(stdClass::class);
    }

    #[Test]
    public function doesNotOverwriteMappingsByDefault(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(new UserMapper($concise)));
        $this->assertTrue($concise->register(new AuthCredentialsMapper()));
        $this->assertTrue($concise->register(new TimestampsMapper()));

        $this->assertFalse($concise->register(new UserMapper($concise)));
        $this->assertFalse($concise->register(new AuthCredentialsMapper()));
        $this->assertFalse($concise->register(new TimestampsMapper()));
    }

    #[Test]
    public function usesADefaultRepository(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(new UserMapper($concise)));
        $this->assertInstanceOf(EntityRepository::class, $concise->repository(User::class));
    }

    #[Test]
    public function canCreateLazyObjects(): void
    {
        $concise = $this->app->make(Concise::class);

        $this->assertTrue($concise->register(new UserMapper($concise)));

        $lazy = $concise->lazy(User::class, 7);

        $this->assertInstanceOf(User::class, $lazy);
        $this->assertSame(7, $lazy->getId());
        $this->assertTrue($lazy->hasId());

        $reflector = new ReflectionClass($lazy);

        $this->assertTrue($reflector->isUninitializedLazyObject($lazy));
    }
}
