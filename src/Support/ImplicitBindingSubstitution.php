<?php
declare(strict_types=1);

namespace Articulate\Concise\Support;

use Articulate\Concise\Concise;
use Articulate\Concise\Contracts\EntityMapper;
use Articulate\Concise\Contracts\RoutableRepository;
use Articulate\Concise\Criteria\ForIdentifier;
use Articulate\Concise\Criterion;
use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Routing\Route;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;

/**
 *
 */
final class ImplicitBindingSubstitution
{
    /**
     * @var \Articulate\Concise\Concise
     */
    private Concise $concise;

    public function __construct(Concise $concise)
    {
        $this->concise = $concise;
    }

    /**
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Routing\Route                 $route
     * @param \Closure():void                           $default
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(
        Container $container,
        Route     $route,
        Closure   $default
    ): void
    {
        // First things first, we run the default implicit binding
        $default();

        // Get the route parameters
        $parameters = $route->parameters();

        // Get the parameters for the route handler
        $handlerParameters = $route->signatureParameters();

        foreach ($handlerParameters as $parameter) {
            $parameterName = $this->getParameterName($parameter->getName(), $route->parameters());

            if ($parameterName === null) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];
            $parameterType  = Reflector::getParameterClassName($parameter);

            /** @var class-string|null $parameterType */

            if ($parameterType === null) {
                continue;
            }

            $mapper = $this->concise->entity($parameterType);

            if ($mapper === null) {
                continue;
            }

            $route->setParameter($parameterName, $this->resolveEntity(
                $mapper,
                $parameterValue,
                $route->bindingFieldFor($parameterName)
            ));
        }
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param string               $name
     * @param array<string, mixed> $parameters
     *
     * @return string|null
     */
    private function getParameterName(string $name, array $parameters): ?string
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }

        return null;
    }

    /**
     * @template EntityObject of object
     *
     * @param \Articulate\Concise\Contracts\EntityMapper<EntityObject> $mapper
     * @param string                                                   $value
     * @param ?string                                                  $bindingField
     *
     * @return object
     *
     * @phpstan-return EntityObject
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function resolveEntity(EntityMapper $mapper, string $value, ?string $bindingField = null): object
    {
        /** @var \Articulate\Concise\Contracts\Repository<EntityObject> $repository */
        $repository = $this->concise->repository($mapper->class());

        if ($repository instanceof RoutableRepository) {
            $entity = $repository->getOneForRouting($value, $bindingField);
        } else {
            $entity = $repository->getOne(Criterion::forIdentifier($value));
        }

        /** @var EntityObject|null $entity */

        if ($entity === null) {
            throw new RecordsNotFoundException('No results for entity [' . $mapper->class() . ']');
        }

        return $entity;
    }
}
