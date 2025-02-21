<?php
declare(strict_types=1);

namespace Articulate\Concise\Tests;

use Articulate\Concise\Support\MapperDiscovery;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\Test;

class MapperDiscoveryTest extends TestCase
{
    use WithWorkbench;

    protected function defineEnvironment($app): void
    {
        MapperDiscovery::noDiscovery();
    }

    #[Test]
    public function hasDefaultPaths(): void
    {
        MapperDiscovery::reset();
        $paths = MapperDiscovery::getPaths();

        $this->assertArrayHasKey(app_path('Mappers/Entities'), $paths);
        $this->assertArrayHasKey(app_path('Mappers/Components'), $paths);
        $this->assertSame('App\\Mappers\\Entities\\', array_values($paths)[0]);
        $this->assertSame('App\\Mappers\\Components\\', array_values($paths)[1]);
    }

    #[Test]
    public function canHaveDefaultPathsDisabled(): void
    {
        MapperDiscovery::reset();
        MapperDiscovery::noDefaults();

        $this->assertEmpty(MapperDiscovery::getPaths());
    }

    #[Test]
    public function canAddCustomPaths(): void
    {
        MapperDiscovery::reset();
        MapperDiscovery::noDefaults();
        MapperDiscovery::addPath(base_path('workbench/app/Mappers/Entities'), 'App\\Mappers\\Entities\\');
        MapperDiscovery::addPath(base_path('workbench/app/Mappers/Components'), 'App\\Mappers\\Components\\');

        $paths = MapperDiscovery::getPaths();

        $this->assertArrayHasKey(base_path('workbench/app/Mappers/Entities'), $paths);
        $this->assertArrayHasKey(base_path('workbench/app/Mappers/Components'), $paths);
        $this->assertSame('App\\Mappers\\Entities\\', array_values($paths)[0]);
        $this->assertSame('App\\Mappers\\Components\\', array_values($paths)[1]);
    }

    #[Test]
    public function canResolveMappers(): void
    {
        MapperDiscovery::reset();
        MapperDiscovery::noDefaults();
        MapperDiscovery::withDiscovery();
        MapperDiscovery::addPath(realpath(__DIR__ . '/../workbench/app/Mappers/Entities'), 'App\\Mappers\\Entities\\');
        MapperDiscovery::addPath(realpath(__DIR__ . '/../workbench/app/Mappers/Components'), 'App\\Mappers\\Components\\');

        $mappers = MapperDiscovery::discover();

        $this->assertNotEmpty($mappers);
        $this->assertCount(3, $mappers);
    }
}
