<?php

namespace Bicycle\Core\Helpers;

class StartHelper
{
    public function preload($currentDirectory) {
        define('SYSTEM_PATH', $currentDirectory.'/system');
        $this->createSystemDirectory(SYSTEM_PATH);
        define('BASE_PATH', $currentDirectory);
        define('APP_PATH', $currentDirectory.'/app');
        define('VENDOR_PATH', $currentDirectory.'/vendor');
        define('GENERATED_PATH', $currentDirectory.'/generated');
        define('CONTAINER_NAME', getenv('CONTAINER_NAME')?:'container');
        define('CONTAINER_DEBUG_MODE', (bool)getenv('CONTAINER_DEBUG_MODE'));
        $this->createGeneratedDirectory(GENERATED_PATH);
    }

    private function createSystemDirectory($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path.'/.gitignore', "*\n!.gitignore");
    }

    private function createGeneratedDirectory(string $GENERATED_PATH)
    {
        if (!file_exists($GENERATED_PATH)) {
            mkdir($GENERATED_PATH, 0777, true);
        }
        file_put_contents($GENERATED_PATH.'/.gitignore', "*\n!.gitignore");
    }
}