<?php

namespace Bicycle\Core\Loaders;

use Bicycle\Core\AttributeInterfaces\LoaderInterface;
use Bicycle\Core\Attributes\Loader;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;

#[Loader(priority: 90)]
class ExceptionTrapLoader implements LoaderInterface
{

    public function load(ContainerBuilder $containerBuilder): void
    {
        $targets = Attributes::findTargetClasses(\Bicycle\Core\Attributes\ExceptionTrap::class);

        foreach ($targets as $target) {
            if (!is_a($target->attribute, \Bicycle\Core\Attributes\ExceptionTrap::class)) {
                continue;
            }
            $target->attribute->setContainerBuilder($containerBuilder);
            $target->attribute->onClass($target->name);
        }
    }
}