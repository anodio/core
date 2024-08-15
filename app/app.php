<?php
require __DIR__ . '/vendor/autoload.php';

use Bicycle\Core\Helpers\StartHelper;

$helper = new StartHelper();
$helper->preload(__DIR__);

$manager = new \Bicycle\Core\ContainerManagement\ContainerManager();
$container = $manager->initContainer();

$container->get('application')->run();


