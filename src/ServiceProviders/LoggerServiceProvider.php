<?php

namespace Anodio\Core\ServiceProviders;

use Anodio\Core\AttributeInterfaces\ServiceProviderInterface;
use Anodio\Core\Attributes\ServiceProvider;
use Anodio\Core\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

#[ServiceProvider]
class LoggerServiceProvider implements ServiceProviderInterface
{

    public function register(\DI\ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            'logger'=>\DI\factory([LoggerFactory::class, 'createLogger']),
            LoggerInterface::class=>\DI\get('logger'),
        ]);
    }
}
