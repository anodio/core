<?php

namespace Bicycle\Core\ServiceProviders;

use Bicycle\Core\AttributeInterfaces\ServiceProviderInterface;
use Bicycle\Core\Attributes\ServiceProvider;

#[ServiceProvider]
class HelloCommandServiceProvider implements ServiceProviderInterface
{

    public function register(\DI\ContainerBuilder $containerBuilder): void
    {
        $a=1;
    }
}