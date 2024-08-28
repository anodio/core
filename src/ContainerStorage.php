<?php

namespace Anodio\Core;

use DI\Container;
use Swow\Coroutine;

class ContainerStorage
{
    public static \SplFixedArray $containers;

    public static int $size = 9999;

    private static int $sizeLen = 4;
    private static Container $mainContainer;

    /**
     * get last three numbers of coroutine number to fit all coroutines by id into size 1000
     * @return void
     */
    public static function getAdoptedCoroutineNumber(): int {
        $coroutineId = Coroutine::getCurrent()->getId();
//        while ($coroutineId > self::$size) {
//            $coroutineId = $coroutineId / 10;
//        }
        if (strlen($coroutineId)>self::$sizeLen) {
            $coroutineId = substr($coroutineId, -self::$sizeLen);
        }
        return $coroutineId;
    }

    public static function getContainer(): Container {
        $coroutineId = self::getAdoptedCoroutineNumber();
        if (!isset(self::$containers[$coroutineId])) {
            throw new \Exception('Container not found');
        }
        return self::$containers[$coroutineId];
    }

    public static function setMainContainer(Container $container) {
        if (isset(self::$mainContainer)) {
            throw new \Exception('Main container already exists');
        }
        self::$mainContainer = $container;
    }

    public static function getMainContainer(): Container {
        return self::$mainContainer;
    }

    public static function setContainer(Container $container): void {
        $coroutineId = self::getAdoptedCoroutineNumber();
        if (isset(self::$containers[$coroutineId])) {
            throw new \Exception('Container already exists');
        }
        self::$containers[$coroutineId] = $container;
    }

    public static function removeContainer(): void {
        $coroutineId = self::getAdoptedCoroutineNumber();
        unset(self::$containers[$coroutineId]);
    }

    public static function init() {
        self::$containers = new \SplFixedArray(self::$size);
    }
}
