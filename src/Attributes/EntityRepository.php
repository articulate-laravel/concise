<?php
declare(strict_types=1);

namespace Articulate\Concise\Attributes;

use Articulate\Concise\Concise;
use Articulate\Concise\Contracts\Repository;
use Attribute;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

/**
 * @template EntityObject of object
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class EntityRepository implements ContextualAttribute
{
    /**
     * @var class-string<EntityObject>
     */
    public string $entity;

    /**
     * @param class-string<EntityObject> $entity
     */
    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param \Articulate\Concise\Attributes\EntityRepository<EntityObject> $attribute
     * @param \Illuminate\Container\Container                               $container
     *
     * @return \Articulate\Concise\Contracts\Repository<EntityObject>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolve(self $attribute, Container $container): Repository
    {
        /** @noinspection NullPointerExceptionInspection */
        return $container->make(Concise::class)->repository($attribute->entity);
    }
}
