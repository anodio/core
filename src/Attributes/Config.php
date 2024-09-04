<?php

namespace Anodio\Core\Attributes;


use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\AttributeInterfaces\AbstractConfig;
use Anodio\Core\Configuration\Env;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Config extends AbstractAttribute
{
    private ContainerBuilder $containerBuilder;
    private bool $allowRedefine = false;

    public function __construct(public string $name)
    {
        if (str_contains($name, '.')) {
            throw new \Exception('The name of the config must not contain a dot');
        }
    }

    public function setAllowRedefine(bool $allowRedefine): void
    {
        $this->allowRedefine = $allowRedefine;
    }

    public function setContainerBuilder(ContainerBuilder $containerBuilder): void
    {
        $this->containerBuilder = $containerBuilder;
    }

    public function onClass(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);
        if (!$reflectionClass->isSubclassOf(AbstractConfig::class)) {
            throw new \Exception('The class ' . $className . ' must extend Anodio\\Core\\AttributeInterfaces\\AbstractConfig');
        }
        if ($reflectionClass->getParentClass()->name!=AbstractConfig::class) {
            //config redefines another one
            $redefines = $reflectionClass->getParentClass()->name;
        } else {
            $redefines = null;
        }

        if ($redefines && !$this->allowRedefine) {
            return false;
        } elseif($redefines && $this->allowRedefine) {
            $nameInContainer = $redefines;
        } else {
            $nameInContainer = $className;
        }

        $data = [];
        foreach (Attributes::findTargetProperties(Env::class) as $target) {
            if ($target->class!=$className) {
                continue;
            }
            if (!property_exists($className, $target->name)) {
                continue;
            }
            $data[$target->name] = $_ENV[$target->attribute->name] ?? $_SERVER[$target->attribute->name] ?? $target->attribute->default;
        }


        $this->containerBuilder->addDefinitions([
            $nameInContainer => \DI\factory(function (string $className, array $data) {

                return new $className($data);
            })->parameter('className', $className)->parameter('data', $data),
            'config.'.$this->name => \DI\get($nameInContainer),
        ]);
        return true;
    }
}
