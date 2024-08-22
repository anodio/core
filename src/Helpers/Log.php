<?php

namespace Anodio\Core\Helpers;

use Psr\Log\LoggerInterface;

class Log
{
    public static function info($message, array $context = [])
    {
        $container = \Anodio\Core\ContainerStorage::getContainer();
        /** @var LoggerInterface $logger */
        $logger = $container->get('logger');
        $logger->info($message, $context);
    }


}