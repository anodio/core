<?php

namespace Anodio\Core\Helpers;

use Anodio\Core\Logger\Context;
use Psr\Log\LoggerInterface;

class Log
{
    public static function info($message, array $context = [])
    {
        static::log('info', $message, $context);
    }

    public static function error($message, array $context = [])
    {
        static::log('error', $message, $context);
    }


    public static function debug($message, array $context = [])
    {
        static::log('debug', $message, $context);
    }

    public static function warning($message, array $context = [])
    {
        static::log('warning', $message, $context);
    }

    public static function critical($message, array $context = [])
    {
        static::log('critical', $message, $context);
    }

    public static function emergency($message, array $context = [])
    {
        static::log('emergency', $message, $context);
    }

    public static function alert($message, array $context = [])
    {
        static::log('alert', $message, $context);
    }

    public static function notice($message, array $context = [])
    {
        static::log('notice', $message, $context);
    }

    public static function log($level, $message, array $context = [])
    {
        $container = \Anodio\Core\ContainerStorage::getContainer();
        foreach (Context::get() as $key=>$value) {
            $context[$key] = $value;
        }
        /** @var LoggerInterface $logger */
        $logger = $container->get('logger');
        $logger->log($level, $message, $context);
    }


}
