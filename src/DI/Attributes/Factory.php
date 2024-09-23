<?php

namespace Anodio\Core\DI\Attributes;

use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\DI\Abstractions\FactoryInterface;
use Attribute;
use DI\ContainerBuilder;
use DI\DependencyException;
use JetBrains\PhpStorm\Deprecated;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
#[Deprecated(reason: 'Use ServiceProvider instead')]
class Factory extends AbstractAttribute
{
    private ContainerBuilder $containerBuilder;

    public function onClass(string $className): bool
    {
        $reflectionClass = new ReflectionClass($className);
        if ($reflectionClass->getConstructor()) {
            throw new DependencyException('Class '.$className.' has constructor. Factory can not have any dependencies.');
        }
        if (!$reflectionClass->implementsInterface(FactoryInterface::class)) {
            throw new DependencyException('Class '.$className.' must implement '.FactoryInterface::class.' interface.');
        }
        /** @var FactoryInterface $factoryInstance */
        $factoryInstance = new $className();
        $factoryInstance->factory($this->containerBuilder);
        unset($factoryInstance);
        return true;
    }

    public function setContainerBuilder(ContainerBuilder $containerBuilder): void
    {
        $this->containerBuilder = $containerBuilder;
    }
}
