<?php

namespace Anodio\Core\Abstraction;

abstract class AbstractAttribute
{
    public function onClass(string $className): bool {
        throw new \Exception('You forgot to redefine');
    }
    public function onMethod(string $className, string $methodName): bool {
        throw new \Exception('You forgot to redefine');
    }
    public function onProperty(string $className, string $propertyName): bool {
        throw new \Exception('You forgot to redefine');
    }
}