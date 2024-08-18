<?php

namespace Anodio\Core\Loaders;

use Anodio\Core\AttributeInterfaces\LoaderInterface;
use Anodio\Core\Attributes\Loader;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;

#[Loader(priority: 90)]
class ExceptionTrapLoader implements LoaderInterface
{

    public function load(ContainerBuilder $containerBuilder): void
    {
        $targets = Attributes::findTargetClasses(\Anodio\Core\Attributes\ExceptionTrap::class);

        foreach ($targets as $target) {
            if (!is_a($target->attribute, \Anodio\Core\Attributes\ExceptionTrap::class)) {
                continue;
            }
            $target->attribute->setContainerBuilder($containerBuilder);
            $target->attribute->onClass($target->name);
        }
    }
}