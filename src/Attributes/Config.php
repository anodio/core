<?php

namespace Anodio\Core\Attributes;


use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\AttributeInterfaces\AbstractConfig;
use Anodio\Core\Configuration\Env;
use Anodio\Core\Configuration\EnvRequired;
use Anodio\Core\Configuration\EnvRequiredNotEmpty;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Config extends AbstractAttribute
{
    private ContainerBuilder $containerBuilder;
    private bool $allowRedefine = false;

    public function __construct(public string $name)
    {

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

        $this->containerBuilder->addDefinitions([
            $nameInContainer => \DI\factory(function (string $className, \Dotenv\Dotenv $dotenv) {

                $data = [];
                foreach (Attributes::findTargetProperties(Env::class) as $target) {
                    if ($target->class!=$className) {
                        continue;
                    }
                    if (!property_exists($className, $target->name)) {
                        continue;
                    }
                    $data[$target->name] = $_ENV[$target->attribute->name] ?? $target->attribute->default;
                }

                foreach (Attributes::findTargetProperties(EnvRequired::class) as $target) {
                    if ($target->class!=$className) {
                        continue;
                    }
                    if (!property_exists($className, $target->name)) {
                        continue;
                    }
                    $dotenv->required($target->attribute->name);
                    $data[$target->name] = $_ENV[$target->attribute->name];
                }

                foreach (Attributes::findTargetProperties(EnvRequiredNotEmpty::class) as $target) {
                    if ($target->class!=$className) {
                        continue;
                    }
                    if (!property_exists($className, $target->name)) {
                        continue;
                    }
                    if (is_null($_ENV[$target->attribute->name])) {
                        throw new \Exception('The env field ' . $target->attribute->name . ' must be set');
                    }
                    $dotenv->required($target->attribute->name)->notEmpty();
                    $data[$target->name] = $_ENV[$target->attribute->name];
                }

                return new $className($data);
            })->parameter('className', $className)->parameter('dotenv', \DI\get(\Dotenv\Dotenv::class)),
            'config.'.$this->name => \DI\get($nameInContainer),
        ]);
        return true;
    }
}