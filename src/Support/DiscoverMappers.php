<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Contracts\Mapper;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class DiscoverMappers
{
    /**
     * The callback to be used to guess class names.
     *
     * @var callable(SplFileInfo, string): class-string|null
     */
    public static $guessClassNamesUsingCallback;

    /**
     * Get all the mappers by searching the given mapper directory.
     *
     * @param string $mapperPath
     * @param string $basePath
     *
     * @return array<class-string<\Articulate\Concise\Contracts\EntityMapper<*>>>
     */
    public static function within(string $mapperPath, string $basePath): array
    {
        $possibleMappers = Finder::create()->files()->in($mapperPath);
        $foundMappers    = [];

        foreach ($possibleMappers as $possibleMapper) {
            try {
                $reflection = new ReflectionClass(
                    static::classFromFile($possibleMapper, $basePath)
                );
            } catch (ReflectionException) {
                continue;
            }

            if (! $reflection->isInstantiable()) {
                continue;
            }

            if ($reflection->implementsInterface(Mapper::class)) {
                $foundMappers[] = $reflection->name;
            }
        }

        /** @var array<class-string<\Articulate\Concise\Contracts\EntityMapper<*>>> $foundMappers */

        return $foundMappers;
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param \SplFileInfo $file
     * @param string       $basePath
     *
     * @return class-string
     */
    protected static function classFromFile(SplFileInfo $file, string $basePath): string
    {
        if (static::$guessClassNamesUsingCallback) {
            return call_user_func(static::$guessClassNamesUsingCallback, $file, $basePath);
        }

        $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        /** @phpstan-ignore return.type */
        return ucfirst(Str::camel(str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())) . '\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        )));
    }

    /**
     * Specify a callback to be used to guess class names.
     *
     * @param callable(SplFileInfo, string): class-string $callback
     *
     * @return void
     */
    public static function guessClassNamesUsing(callable $callback): void
    {
        static::$guessClassNamesUsingCallback = $callback;
    }
}
