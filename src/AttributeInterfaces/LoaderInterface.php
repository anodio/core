<?php

namespace Bicycle\Core\AttributeInterfaces;

use DI\ContainerBuilder;

interface LoaderInterface
{
    public function load(ContainerBuilder $containerBuilder): void;
}