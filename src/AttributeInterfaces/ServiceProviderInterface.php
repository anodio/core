<?php

namespace Bicycle\Core\AttributeInterfaces;

interface ServiceProviderInterface
{
    public function register(\DI\ContainerBuilder $containerBuilder): void;
}