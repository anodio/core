<?php

namespace Anodio\Core\DI\Abstractions;

use DI\ContainerBuilder;

interface FactoryInterface
{
    public function factory(ContainerBuilder $containerBuilder): void;
}
