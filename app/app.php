<?php

ini_set('opcache.enable', 0);
ini_set('opcache.enable_cli', 0);

use Anodio\Core\ContainerStorage;
use Anodio\Core\Helpers\StartHelper;

require __DIR__ . '/vendor/autoload.php';

$helper = new StartHelper();
$helper->preload(__DIR__);

$manager = new \Anodio\Core\ContainerManagement\ContainerManager();
\Anodio\Core\ContainerStorage::init();
$container = $manager->initContainer();
ContainerStorage::setContainer($container);
ContainerStorage::setMainContainer($container);

$container->get('application')->run();


