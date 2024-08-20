<?php


function config(string $key, mixed $default = null): mixed
{
    $explodedKey = explode('.', $key);
    $configName = $explodedKey[0];
    unset($explodedKey[0]);
    $container = \Anodio\Core\ContainerStorage::getContainer();
    $configObject = (array)$container->get('config.'.$configName);
    foreach ($explodedKey as $key) {
        if (!isset($configObject[$key])) {
            return $default;
        }
        $configObject = $configObject[$key];
    }
    return $configObject;
}

function app(mixed $name=null) {
    if (!is_null($name)) {
        return \Anodio\Core\ContainerStorage::getContainer()->make($name);
    } else {
        return \Anodio\Core\ContainerStorage::getContainer();
    }
}