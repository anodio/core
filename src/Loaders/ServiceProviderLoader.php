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
        $targets = Attributes::findTargetClasses(ServiceProvider::class);
        $targets = $this->sort($targets);
        foreach ($targets as $target) {
            if (!is_a($target->attribute, ServiceProvider::class)) {
                continue;
            }
            $target->attribute->setContainerBuilder($containerBuilder);
            $target->attribute->onClass($target->name);
        }
    }

    /**
     * @param array $targets
     * @return array
     */
    private function sort(array $targets): array
    {
        usort($targets, function ($a, $b) {
            if ($a->attribute->priority < $b->attribute->priority) {
                return 1;
            }
            if ($a->attribute->priority > $b->attribute->priority) {
                return -1;
            }
            return 0;
        });
        return $targets;
    }

}
