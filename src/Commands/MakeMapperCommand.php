<?php
declare(strict_types=1);

namespace Articulate\Concise\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('make:mapper')]
class MakeMapperCommand extends GeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:mapper {name : The name of the class that\'s being mapped}
        {class : The entity that\'s being mapped}
        {--entity : Create an entity mapper}
        {--component : Create a component mapper}
        {--table= : The entity table}
        {--identity= : The entity identity field}
        {--connection= : The connection the entity uses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new entity mapper';

    protected $type = 'Mapper';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('component')) {

        }

        return $this->resolveStubPath('/../../resources/stubs/mapper.entity.stub');
    }

    /**
     * Resolve the fully qualified path to the stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\Mappers\\' . ($this->option('component') ? 'Components' : 'Entities');
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name): string
    {
        /** @var string|null $identity */
        $identity = $this->option('identity');
        /** @var string|null $table */
        $table = $this->option('table');
        /** @var string|null $connection */
        $connection = $this->option('connection');
        $class      = class_basename($name);

        if ($this->option('component')) {
            $fqn = 'App\\Components\\' . $this->argument('class');
        } else {
            $fqn = 'App\\Entities\\' . $this->argument('class');
        }

        /** @var array<string, string> $replacements */
        $replacements = [
            '{{ extraMethods }}'   => $this->buildExtraMethods($identity, $table, $connection),
            '{{ class }}'          => $this->argument('name'),
            '{{ entityClass }}'    => $fqn,
            '{{ componentClass }}' => $fqn,
            '{{ identity }}'       => $identity ?? '',
            '{{ table }}'          => $table ?? '',
            '{{ connection }}'     => $connection ?? '',
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            parent::buildClass($name)
        );
    }

    protected function buildExtraMethods(?string $identity, ?string $table, ?string $connection): string
    {
        $methods = '';

        if ($identity !== null) {
            $methods .= "\r\n" . file_get_contents($this->resolveStubPath('/../../resources/stubs/mapper.entity.identity.stub'));
        }

        if ($table !== null) {
            $methods .= "\r\n" . file_get_contents($this->resolveStubPath('/../../resources/stubs/mapper.entity.table.stub'));
        }

        if ($connection !== null) {
            $methods .= "\r\n" . file_get_contents($this->resolveStubPath('/../../resources/stubs/mapper.entity.connection.stub'));
        }

        return $methods;
    }
}
