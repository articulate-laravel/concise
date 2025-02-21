<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Contracts\Mapper;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 *
 */
final class MapperDiscovery
{
    private static bool $discover = true;

    /**
     * Mapped paths to namespaces
     *
     * @var array<string, string>
     */
    private static array $pathMappings = [];

    /**
     * @var bool
     */
    private static bool $useDefaults = true;

    /**
     * @param string      $path
     * @param string|null $namespace
     *
     * @return void
     */
    public static function addPath(string $path, ?string $namespace = null): void
    {
        if (Str::startsWith($path, app_path())) {
            self::$pathMappings[Str::rtrim($path, DIRECTORY_SEPARATOR)] = $namespace ?? app()->getNamespace();
        } else {
            if ($namespace === null) {
                throw new InvalidArgumentException('Paths outside the app path must have a namespace.');
            }

            self::$pathMappings[Str::rtrim($path, DIRECTORY_SEPARATOR)] = $namespace;
        }
    }

    /**
     * @return void
     */
    public static function withDefaults(): void
    {
        self::$useDefaults = true;
    }

    public static function noDiscovery(): void
    {
        self::$discover = false;
    }

    public static function withDiscovery(): void
    {
        self::$discover = true;
    }

    /**
     * @return void
     */
    public static function noDefaults(): void
    {
        self::$useDefaults = false;
    }

    public static function reset(): void
    {
        self::$useDefaults  = true;
        self::$pathMappings = [];
    }

    /**
     * @return array<string, string>
     */
    public static function getDefaultPaths(): array
    {
        return [
            app_path('Mappers/Entities')   => app()->getNamespace() . 'Mappers\\Entities\\',
            app_path('Mappers/Components') => app()->getNamespace() . 'Mappers\\Components\\',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function getPaths(): array
    {
        return array_merge(
            self::$useDefaults ? self::getDefaultPaths() : [],
            self::$pathMappings
        );
    }

    /**
     * @return array<class-string<\Articulate\Concise\Contracts\Mapper<object>>>
     */
    public static function discover(): array
    {
        if (! self::$discover) {
            return [];
        }

        $mappers = [];
        $paths   = self::getPaths();

        foreach ($paths as $path => $namespace) {
            self::discoverMappers($path, $namespace, $mappers);
        }

        return $mappers;
    }

    /**
     * @param string $path
     * @param string $namespace
     * @param array<class-string<\Articulate\Concise\Contracts\Mapper<object>>>  $mappers
     *
     * @return void
     */
    private static function discoverMappers(string $path, string $namespace, array &$mappers): void
    {
        $files = Finder::create()->files()->in($path);

        foreach ($files as $file) {
            try {
                $reflector = new ReflectionClass(self::classFromFile($file, $path, $namespace));
            } catch (ReflectionException) {
                continue;
            }

            if (! $reflector->isInstantiable()) {
                continue;
            }

            if (! $reflector->isSubclassOf(Mapper::class)) {
                continue;
            }

            /** @var class-string<\Articulate\Concise\Contracts\Mapper<object>> $class */
            $class = $reflector->getName();

            $mappers[] = $class;
        }
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param \SplFileInfo $file
     * @param string       $path
     * @param string       $namespace
     *
     * @return class-string
     */
    protected static function classFromFile(SplFileInfo $file, string $path, string $namespace): string
    {
        $class = trim(Str::replaceFirst($path, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        /** @var class-string */
        return $namespace . ucfirst(Str::camel(str_replace(
            [DIRECTORY_SEPARATOR, $path . '\\'],
            ['\\', $namespace],
            ucfirst(Str::replaceLast('.php', '', $class))
        )));
    }
}
