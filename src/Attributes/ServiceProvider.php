<?php

namespace Anodio\Core\Attributes;

use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\AttributeInterfaces\ServiceProviderInterface;
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
            throw new \Exception('The class ' . $className . ' must implement Anodio\\Core\\AttributeInterfaces\\ServiceProviderInterface');
        }

        /** @var ServiceProviderInterface $provider */
        $provider = new $className();
        $provider->register($this->containerBuilder);

        return true;
    }
}