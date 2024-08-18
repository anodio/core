<?php

namespace Anodio\Core\AttributeInterfaces;

interface ServiceProviderInterface
{
    public function register(\DI\ContainerBuilder $containerBuilder): void;
}