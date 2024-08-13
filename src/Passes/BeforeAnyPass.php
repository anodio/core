<?php

namespace Bicycle\Core\Passes;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BeforeAnyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // ... do something during the compilation
    }
}