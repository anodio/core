<?php

namespace src;

use DI\Container;
use Swow\Coroutine;

class CoroutineStorage
{
    public static \SplFixedArray $containers;
    public static \SplFixedArray $ids;

    public static int $size = 5;

    public static function getContainer(): ?Container {
        $coroutineId = Coroutine::getCurrent()->getId();
        $containerId = self::$ids[$coroutineId];
        return (isset(self::$containers[$containerId]))?self::$containers[$containerId]:null;
    }

    public static function setContainer(Container $container): void {
        $coroutineId = Coroutine::getCurrent()->getId();
        for ($i=0; $i<self::$size; $i++) {
            if (!isset(self::$containers[$i])) {
                $containerId = $i;
                break;
            }
        }
        if (!isset($containerId)) {
            throw new \Exception('No free container slots');
        }
        self::$ids[$coroutineId] = $containerId;
        self::$containers[$containerId] = $container;
    }

    public static function removeContainer(): void {
        $coroutineId = Coroutine::getCurrent()->getId();
        $containerId = self::$ids[$coroutineId];
        unset(self::$containers[$containerId]);
    }

    public static function init() {
        self::$containers = new \SplFixedArray(self::$size);
    }
}