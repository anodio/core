<?php

namespace Bicycle\Core\Loaders;
use Bicycle\Core\AttributeInterfaces\LoaderInterface;
use Bicycle\Core\AttributeInterfaces\ServiceProviderInterface;
use Bicycle\Core\Attributes\Loader;
use Bicycle\Core\Attributes\ServiceProvider;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;

#[Loader(priority: 80)]
class ServiceProviderLoader implements LoaderInterface
{
    public function load(ContainerBuilder $containerBuilder): void
    {
        foreach (Attributes::findTargetClasses(ServiceProvider::class) as $target) {
                if (!is_a($target->attribute, ServiceProvider::class)) {
                    continue;
                }
                $target->attribute->setContainerBuilder($containerBuilder);
                $target->attribute->onClass($target->name);
            }
        }

}