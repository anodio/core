<?php

namespace Anodio\Core\ContainerManagement;

use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\AttributeInterfaces\LoaderInterface;
use Anodio\Core\Attributes\Loader;
use DI\Container;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;
use Psr\Container\ContainerInterface;

class ContainerManager
{
    private static ContainerBuilder $builder;
    public static function createContainer(): Container {
        return static::$builder->build();
    }

    public function initContainer(): Container
    {
        self::$builder = new \DI\ContainerBuilder();
        self::$builder->enableCompilation(SYSTEM_PATH.'/cnt_'.CONTAINER_NAME);
        self::$builder->useAttributes(true);
        if ($this->vendorChanged() || $this->appChanged() || !file_exists(SYSTEM_PATH.'/cnt_'.CONTAINER_NAME)) {
            shell_exec('rm -rf '.SYSTEM_PATH.'/cnt_'.CONTAINER_NAME);
            $this->runLoaders(self::$builder);
        }
        self::$builder->addDefinitions([
            Container::class=>\DI\factory(function(ContainerInterface $c) {
                return $c;
            })
        ]);

        $container = self::$builder->build();
        if ($this->vendorChanged() || $this->appChanged()) {
            $this->updateHashes();
        }
        return $container;
    }

    private function sortDescending($loaders)
    {
        $fnc = function($loaderA, $loaderB) {
            if ($loaderA->attribute->priority == $loaderB->attribute->priority) {
                return 0;
            }
            return ($loaderA->attribute->priority > $loaderB->attribute->priority) ? -1 : 1;
        };
        usort($loaders, $fnc);

        return $loaders;
    }

    private function runLoaders(ContainerBuilder $builder) {
        $loaders = Attributes::findTargetClasses(Loader::class);
        $loaders = $this->sortDescending($loaders);
        foreach ($loaders as $target) {
            if (!is_subclass_of($target->attribute, AbstractAttribute::class)) {
                continue;
            }
            if (!is_a($target->attribute, Loader::class)) {
                continue;
            }
            $target->attribute->setContainerBuilder($builder);
            $target->attribute->onClass($target->name);
        }
    }

    private function vendorChanged(): bool
    {
        return $this->getVendorHashFromCache() !== $this->collectVendorHash();
    }

    private function appChanged(): bool {
        return $this->getAppHashFromCache() !== $this->getHashOfAllMimeTypes(BASE_PATH.'/app');
    }

    private function getVendorHashFromCache(): string {
        if (!file_exists(SYSTEM_PATH . '/hashes/vendor_hash')) {
            @mkdir(SYSTEM_PATH . '/hashes', 0777, true);
            file_put_contents(SYSTEM_PATH . '/hashes/vendor_hash', '10101010101010');
        }
        return file_get_contents(SYSTEM_PATH . '/hashes/vendor_hash');
    }

    private function getAppHashFromCache(): string {
        if (!file_exists(SYSTEM_PATH . '/hashes/app_hash')) {
            @mkdir(SYSTEM_PATH . '/hashes', 0777, true);
            file_put_contents(SYSTEM_PATH . '/hashes/app_hash', '10101010101010');
        }
        return file_get_contents(SYSTEM_PATH . '/hashes/app_hash');
    }

    private function collectVendorHash(): string {
        return md5_file(BASE_PATH.'/composer.lock');
    }

    /**
     * update hash of composer.lock and app directory
     * @return void
     */
    private function updateHashes(): void {
        $hash = $this->collectVendorHash();
        file_put_contents(SYSTEM_PATH . '/hashes/vendor_hash', $hash);

        $appHash = $this->getHashOfAllMimeTypes(BASE_PATH.'/app');
        file_put_contents(SYSTEM_PATH . '/hashes/app_hash', $appHash);
    }

    private function getHashOfAllMimeTypes(string $path, $hash = ''): string
    {
        $files = glob($path . "/*");
        foreach ($files as $file) {
            if (is_dir($file)) {
                $hash = hash('xxh3',   $this->getHashOfAllMimeTypes($file, $hash));
            } else {
                $hash = hash('xxh3',   $hash.filemtime($file));
            }
        }
        if (file_exists(BASE_PATH.'/.env')) {
            $hash = hash('xxh3',   $hash.filemtime(BASE_PATH.'/.env'));
        }
        return $hash;
    }
}