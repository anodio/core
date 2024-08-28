<?php

namespace Anodio\Core\Attributes;

use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\AttributeInterfaces\LoaderInterface;
use DI\ContainerBuilder;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Loader extends AbstractAttribute
{
    public int $priority = 0;
    private ContainerBuilder $containerBuilder;

    public function __construct(int $priority = 0)
    {
        $this->priority = $priority;
    }

    public function setContainerBuilder(ContainerBuilder $containerBuilder) {
        $this->containerBuilder = $containerBuilder;
    }

    public function onClass(string $className): bool
    {
        try {
            if (!is_subclass_of($className, \Anodio\Core\AttributeInterfaces\LoaderInterface::class)) {
                throw new \Exception('The class ' . $className . ' must implement Anodio\\Core\\AttributeInterfaces\\LoaderInterface');
            }
            /** @var LoaderInterface $exemplar */
            $exemplar = new $className();
            $exemplar->load($this->containerBuilder);
        } catch (\Throwable $e) {
            echo $e->getMessage()."\n";
            echo $e->getFile().':'.$e->getLine()."\n";
            echo $e->getTraceAsString()."\n";
            exit(1);
        }

        return true;
    }
}
