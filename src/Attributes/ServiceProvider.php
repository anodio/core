<?php

namespace Bicycle\Core\Attributes;

use Bicycle\Core\Abstraction\AbstractAttribute;
use Bicycle\Core\AttributeInterfaces\ServiceProviderInterface;
use DI\ContainerBuilder;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ServiceProvider extends AbstractAttribute
{
    public int $priority = 0;

    public ContainerBuilder $containerBuilder;

    public function setContainerBuilder(ContainerBuilder $containerBuilder) {
        $this->containerBuilder = $containerBuilder;
    }

    public function __construct(int $priority = 0)
    {
        $this->priority = $priority;
    }

    public function onClass(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);
        if (!$reflectionClass->implementsInterface(ServiceProviderInterface::class)) {
            throw new \Exception('The class ' . $className . ' must implement Bicycle\\Core\\AttributeInterfaces\\ServiceProviderInterface');
        }

        /** @var ServiceProviderInterface $provider */
        $provider = new $className();
        $provider->register($this->containerBuilder);

        return true;
    }
}