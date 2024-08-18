<?php

namespace Anodio\Core\Helpers;

class StartHelper
{
    public function preload($currentDirectory) {
        define('SYSTEM_PATH', $currentDirectory.'/system');
        $this->createSystemDirectory(SYSTEM_PATH);
        define('BASE_PATH', $currentDirectory);
        define('APP_PATH', $currentDirectory.'/app');
        define('VENDOR_PATH', $currentDirectory.'/vendor');
        define('CONTAINER_NAME', getenv('CONTAINER_NAME') ?: 'cli');
    }

    private function createSystemDirectory($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path.'/.gitignore', "*\n!.gitignore");
    }

}