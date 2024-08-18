<?php

use Anodio\Core\Helpers\StartHelper;

require __DIR__ . '/vendor/autoload.php';

$helper = new StartHelper();
$helper->preload(__DIR__);

$manager = new \Anodio\Core\ContainerManagement\ContainerManager();
$container = $manager->initContainer();
\Anodio\Core\ContainerStorage::init();

$container->get('application')->run();


