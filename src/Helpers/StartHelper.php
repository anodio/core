<?php

namespace Anodio\Core\Helpers;

class StartHelper
{
    public function preload($currentDirectory) {
        require_once dirname(__FILE__) . '/../../helpers/laravel-helpers.php';
        if (!defined('SYSTEM_PATH')) define('SYSTEM_PATH', $currentDirectory.'/system');
        $this->createSystemDirectory(SYSTEM_PATH);
        if (!defined('BASE_PATH')) define('BASE_PATH', $currentDirectory);
        if (!defined('APP_PATH')) define('APP_PATH', $currentDirectory.'/app');
        if (!defined('VENDOR_PATH')) define('VENDOR_PATH', $currentDirectory.'/vendor');
        if (!defined('CONTAINER_NAME')) define('CONTAINER_NAME', (getenv('CONTAINER_NAME') ?? $_SERVER['CONTAINER_NAME']) ?: 'cli');
    }

    private function createSystemDirectory($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            file_put_contents($path.'/.gitignore', "*\n!.gitignore");
        }
    }

}
