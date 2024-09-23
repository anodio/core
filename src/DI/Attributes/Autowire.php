<?php

namespace Anodio\Core\DI\Attributes;

use Anodio\Core\Abstraction\AbstractAttribute;
use JetBrains\PhpStorm\Deprecated;

#[\Attribute(\Attribute::TARGET_CLASS)]
#[Deprecated(reason: 'Use ServiceProvider instead')]
class Autowire extends AbstractAttribute
{
    private \DI\ContainerBuilder $containerBuilder;

    public function setContainerBuilder(\DI\ContainerBuilder $containerBuilder): void
    {
        $this->containerBuilder = $containerBuilder;
    }
    public function onClass(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);
        if (count($reflectionClass->getInterfaceNames())>0) {
            throw new \DI\DependencyException('Class '.$className.' has interfaces. It should be mapped using ServiceProvider');
        }
        $this->containerBuilder->addDefinitions([
            $className =>\DI\autowire($className),
        ]);
        return true;
    }
}
