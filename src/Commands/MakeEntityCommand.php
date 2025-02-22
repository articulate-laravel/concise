<?php
declare(strict_types=1);

namespace Articulate\Concise\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('make:entity')]
class MakeEntityCommand extends GeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:entity {name : The name of the entity}
        {--table= : The entity table}
        {--identity= : The entity identity field}
        {--connection= : The connection the entity uses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new entity';

    protected $type = 'Entity';

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): bool|null
    {
        if (parent::handle() === false) {
            return false;
        }

        $this->call('make:mapper', [
            'name'         => $this->argument('name') . 'Mapper',
            'class'        => $this->argument('name'),
            '--identity'   => $this->option('identity'),
            '--table'      => $this->option('table'),
            '--connection' => $this->option('connection'),
        ]);

        return null;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/../../resources/stubs/entity.stub');
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
        return $rootNamespace . '\\Entities';
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
        /** @var string $identity */
        $identity = $this->option('identity') ?? 'id';

        return str_replace(
            ['{{ identityCamel }}', '{{ identity }}'],
            [$identity, Str::camel($identity)],
            parent::buildClass($name)
        );
    }
}
