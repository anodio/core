<?php

use Anodio\Core\ContainerStorage;
use Anodio\Core\Helpers\StartHelper;

require __DIR__ . '/vendor/autoload.php';

$helper = new StartHelper();
$helper->preload(__DIR__);

$manager = new \Anodio\Core\ContainerManagement\ContainerManager();
\Anodio\Core\ContainerStorage::init();
$container = $manager->initContainer();
ContainerStorage::setContainer($container);

$container->get('application')->run();


