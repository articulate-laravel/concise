<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Concise;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MapperServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string<\Articulate\Concise\Contracts\EntityMapper<*>>>
     */
    protected array $mappers = [];

    /**
     * Indicates if mappers should be discovered.
     *
     * @var bool
     */
    protected static bool $shouldDiscoverMappers = true;

    /**
     * The configured mapper discovery paths.
     *
     * @var array<string>
     */
    protected static array $mapperDiscoveryPaths = [];

    public function register(): void
    {
        $this->app->afterResolving(Concise::class, function (Concise $concise) {
            $mappers = $this->getMappers();

            foreach ($mappers as $mapper) {
                /** @phpstan-ignore argument.type */
                $concise->register($mapper);
            }
        });
    }

    /**
     * @return array<class-string<\Articulate\Concise\Contracts\Mapper<*>>>
     */
    private function getMappers(): array
    {
        return array_merge(
            $this->discoveredMappers(),
            $this->mappers
        );
    }

    /**
     * Get the discovered mappers for the application.
     *
     * @return array<class-string<\Articulate\Concise\Contracts\Mapper<*>>>
     */
    protected function discoveredMappers(): array
    {
        return $this->shouldDiscoverMappers()
            ? $this->discoverMappers()
            : [];
    }

    /**
     * Determine if mappers should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverMappers(): bool
    {
        return get_class($this) === __CLASS__ && static::$shouldDiscoverMappers === true;
    }

    /**
     * Discover the mappers for the application.
     *
     * @return array<class-string<\Articulate\Concise\Contracts\Mapper<*>>>
     */
    public function discoverMappers(): array
    {
        return (new Collection($this->discoverMappersWithin()))
            /** @phpstan-ignore argument.type */
            ->flatMap(function (string $directory) {
                return glob($directory, GLOB_ONLYDIR);
            })
            ->reject(function (string $directory) {
                return ! is_dir($directory);
            })
            ->reduce(function ($discovered, $directory) {
                return array_merge_recursive(
                    $discovered,
                    DiscoverMappers::within($directory, $this->mapperDiscoveryBasePath())
                );
            }, []);
    }

    /**
     * Get the directories that should be used to discover mappers.
     *
     * @return array<string>
     */
    protected function discoverMappersWithin(): array
    {
        return static::$mapperDiscoveryPaths ?? [
            $this->app->path('Mappers\Components'),
            $this->app->path('Mappers\Entities'),
        ];
    }

    /**
     * Add the given mapper discovery paths to the application's mapper discovery paths.
     *
     * @param string|array<string> $paths
     *
     * @return void
     */
    public static function addMapperDiscoveryPaths(array|string $paths): void
    {
        static::$mapperDiscoveryPaths = array_values(array_unique(
            array_merge(static::$mapperDiscoveryPaths, Arr::wrap($paths))
        ));
    }

    /**
     * Set the globally configured mapper discovery paths.
     *
     * @param array<string> $paths
     *
     * @return void
     */
    public static function setMapperDiscoveryPaths(array $paths): void
    {
        static::$mapperDiscoveryPaths = $paths;
    }

    /**
     * Get the base path to be used during mapper discovery.
     *
     * @return string
     */
    protected function mapperDiscoveryBasePath(): string
    {
        return base_path();
    }

    /**
     * Disable mapper discovery for the application.
     *
     * @return void
     */
    public static function disableMapperDiscovery(): void
    {
        static::$shouldDiscoverMappers = false;
    }
}
