<?php

namespace Anodio\Core\Loaders;
use Anodio\Core\AttributeInterfaces\LoaderInterface;
use Anodio\Core\AttributeInterfaces\ServiceProviderInterface;
use Anodio\Core\Attributes\Loader;
use Anodio\Core\Attributes\ServiceProvider;
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