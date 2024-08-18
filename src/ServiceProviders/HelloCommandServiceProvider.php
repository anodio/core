<?php

namespace Anodio\Core\ServiceProviders;

use Anodio\Core\AttributeInterfaces\ServiceProviderInterface;
use Anodio\Core\Attributes\ServiceProvider;

#[ServiceProvider]
class HelloCommandServiceProvider implements ServiceProviderInterface
{

    public function register(\DI\ContainerBuilder $containerBuilder): void
    {
        $a=1;
    }
}