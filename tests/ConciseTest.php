<?php
declare(strict_types=1);

namespace Articulate\Concise\Tests;

use App\Components\AuthCredentials;
use App\Components\Timestamps;
use App\Entities\User;
use App\Mappers\Components\AuthCredentialsMapper;
use App\Mappers\Components\TimestampsMapper;
use App\Mappers\Entities\UserMapper;
use App\Providers\MapperServiceProvider;
use Articulate\Concise\Concise;
use Articulate\Concise\ConciseServiceProvider;
use PHPUnit\Framework\Attributes\Test;

class ConciseTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            MapperServiceProvider::class,
        ];
    }

    #[Test]
    public function serviceProviderHasBeenRegistered(): void
    {
        $this->assertTrue($this->app->providerIsLoaded(ConciseServiceProvider::class));
    }

    #[Test]
    public function isBoundToTheContainer(): void
    {
        $this->assertTrue($this->app->bound(Concise::class));
    }

    #[Test]
    public function hasRegisteredMappersSuccessfully(): void
    {
        $this->assertTrue($this->app->providerIsLoaded(MapperServiceProvider::class));

        $concise = $this->app->make(Concise::class);

        $entityMappers = $concise->getEntityMappers();

        $this->assertNotEmpty($entityMappers);
        $this->assertCount(1, $entityMappers);
        $this->assertArrayHasKey(User::class, $entityMappers);
        $this->assertInstanceOf(UserMapper::class, $entityMappers[User::class]);

        $componentMappers = $concise->getComponentMappers();

        $this->assertNotEmpty($componentMappers);
        $this->assertCount(2, $componentMappers);
        $this->assertArrayHasKey(AuthCredentials::class, $componentMappers);
        $this->assertArrayHasKey(Timestamps::class, $componentMappers);
        $this->assertInstanceOf(AuthCredentialsMapper::class, $componentMappers[AuthCredentials::class]);
        $this->assertInstanceOf(TimestampsMapper::class, $componentMappers[Timestamps::class]);
    }
}
