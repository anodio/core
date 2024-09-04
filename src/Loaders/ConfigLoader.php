<?php

namespace Anodio\Core\Loaders;

use Anodio\Core\AttributeInterfaces\LoaderInterface;
use Anodio\Core\Attributes\Config;
use Anodio\Core\Attributes\Loader;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;

#[Loader(priority: 100)]
class ConfigLoader implements LoaderInterface
{
    public function load(ContainerBuilder $containerBuilder): void
    {
        $targets = Attributes::findTargetClasses(Config::class);

        $dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
        $dotenv->safeLoad();

        foreach ($targets as $key=>$target) {
            if (!is_a($target->attribute, Config::class)) {
                unset($targets[$key]);
                continue;
            }
            $target->attribute->setContainerBuilder($containerBuilder);
            $target->attribute->setAllowRedefine(false);
            $result = $target->attribute->onClass($target->name);
            if ($result===true) {
                unset($targets[$key]);
            }
        }
        foreach ($targets as $target) {
            $target->attribute->setAllowRedefine(true);
            $target->attribute->onClass($target->name);
        }
    }
}
