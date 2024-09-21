<?php

namespace Anodio\Core\DI\ServiceProviders;

use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\AttributeInterfaces\ServiceProviderInterface;
use Anodio\Core\Attributes\ServiceProvider;
use Anodio\Core\DI\Abstractions\FactoryInterface;
use olvlvl\ComposerAttributeCollector\Attributes;

#[ServiceProvider]
class DiServiceProvider implements ServiceProviderInterface
{

    public function register(\DI\ContainerBuilder $containerBuilder): void
    {
        $autowireAttributes = Attributes::findTargetClasses(\Anodio\Core\DI\Attributes\Autowire::class);
        foreach ($autowireAttributes as $autowireAttribute) {
            if (!($autowireAttribute->attribute instanceof \Anodio\Core\DI\Attributes\Autowire)) {
                continue;
            }
            $autowireAttribute->attribute->setContainerBuilder($containerBuilder);
            $autowireAttribute->attribute->onClass($autowireAttribute->name);
        }

        $factoryAttributes = Attributes::findTargetClasses(\Anodio\Core\DI\Attributes\Factory::class);
        foreach ($factoryAttributes as $factoryAttribute) {
            if (!($factoryAttribute->attribute instanceof \Anodio\Core\DI\Attributes\Factory)) {
                continue;
            }
            $reflectionFactroyClass = new \ReflectionClass($factoryAttribute->name);
            if (!$reflectionFactroyClass->implementsInterface(FactoryInterface::class)) {
                throw new \DI\DependencyException('Class '.$factoryAttribute->name.' must implement '.FactoryInterface::class.' interface.');
            }
            $factoryAttribute->attribute->setContainerBuilder($containerBuilder);
            $factoryAttribute->attribute->onClass($factoryAttribute->name);
        }

    }
}
