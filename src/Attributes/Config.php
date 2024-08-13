<?php

namespace Bicycle\Core\Attributes;


use Bicycle\Core\Abstraction\AbstractAttribute;
use Bicycle\Core\AttributeInterfaces\ConfigInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Config extends AbstractAttribute
{
    public function __construct(public string $name)
    {

    }

    public function onClass(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);
        if (!$reflectionClass->hasMethod('load')) {
            throw new \Exception('The class ' . $className . ' must implement Bicycle\\Core\\AttributeInterfaces\\ConfigInterface');
        }
        if (!$reflectionClass->isReadOnly()) {
            throw new \Exception('The class ' . $className . ' must be read-only');
        }
        /** @var ConfigInterface $exemplar */
        $exemplar = call_user_func("$className::load");
//        $this->container->setDefinition(
//            'config.'.$this->name,
//            new Definition($className)
//        );
//
//        $this->container->set('config.'.$this->name, $exemplar);
//        $this->container->set($className, $exemplar);
        return true;
    }
}