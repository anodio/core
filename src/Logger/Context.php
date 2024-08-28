<?php

namespace Anodio\Core\Logger;

use Anodio\Core\ContainerStorage;

class Context
{
    public static function addHidden(string $key, mixed $value): void
    {
        $container = ContainerStorage::getContainer();
        $context = $container->has('logger.context-hidden') ? $container->get('logger.context-hidden') : new \stdClass();
        $context->{$key} = $value;
        $container->set('logger.context-hidden', $context);
    }

    public static function getHiddenByKey(string $key): mixed
    {
        $container = ContainerStorage::getContainer();
        $context = $container->has('logger.context-hidden') ? $container->get('logger.context-hidden') : new \stdClass();
        return $context->{$key} ?? null;
    }

    public static function add(string $key, mixed $value): void
    {
        $container = ContainerStorage::getContainer();
        $context = $container->has('logger.context') ? $container->get('logger.context') : new \stdClass();
        $context->{$key} = $value;
        $container->set('logger.context', $context);
    }

    public static function get(): \stdClass
    {
        $container = ContainerStorage::getContainer();
        return $container->has('logger.context') ? $container->get('logger.context') : new \stdClass();
    }

    public static function getHidden(): \stdClass
    {
        $container = ContainerStorage::getContainer();
        return $container->has('logger.context-hidden') ? $container->get('logger.context-hidden') : new \stdClass();
    }

    public static function getByKey(string $key): mixed
    {
        $container = ContainerStorage::getContainer();
        $context = $container->has('logger.context') ? $container->get('logger.context') : new \stdClass();
        return $context->{$key} ?? null;
    }
}
