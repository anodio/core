<?php

namespace Bicycle\Core\Attributes;

use Bicycle\Core\Abstraction\AbstractAttribute;
use Bicycle\Core\AttributeInterfaces\LoaderInterface;
use DI\ContainerBuilder;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Loader extends AbstractAttribute
{
    public int $priority = 0;
    private ContainerBuilder $containerBuilder;

    public function __construct(int $priority = 0)
    {
        $this->priority = $priority;
    }

    public function setContainerBuilder(ContainerBuilder $containerBuilder) {
        $this->containerBuilder = $containerBuilder;
    }

    public function onClass(string $className): bool
    {
        if (!is_subclass_of($className, \Bicycle\Core\AttributeInterfaces\LoaderInterface::class)) {
            throw new \Exception('The class ' . $className . ' must implement Bicycle\\Core\\AttributeInterfaces\\LoaderInterface');
        }
        /** @var LoaderInterface $exemplar */
        $exemplar = new $className();
        $exemplar->load($this->containerBuilder);
        return true;
    }
}