<?php

namespace Anodio\Core\ContainerManagement;

use Anodio\Core\Abstraction\AbstractAttribute;
use Anodio\Core\Attributes\Loader;
use Composer\IO\NullIO;
use DI\Container;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;
use olvlvl\ComposerAttributeCollector\ClassAttributeCollector;
use olvlvl\ComposerAttributeCollector\Datastore\RuntimeDatastore;
use olvlvl\ComposerAttributeCollector\Filter\Chain;
use olvlvl\ComposerAttributeCollector\Filter\ContentFilter;
use olvlvl\ComposerAttributeCollector\Filter\InterfaceFilter;
use olvlvl\ComposerAttributeCollector\MemoizeAttributeCollector;
use olvlvl\ComposerAttributeCollector\MemoizeClassMapFilter;
use olvlvl\ComposerAttributeCollector\MemoizeClassMapGenerator;
use olvlvl\ComposerAttributeCollector\TransientCollectionRenderer;
use Psr\Container\ContainerInterface;

class ContainerManager
{
    private static ContainerBuilder $builder;
    public static function createContainer($enableCompilation = true): Container {
        if (!isset(static::$builder)) {
            $containerManager = new ContainerManager();
            $container = $containerManager->initContainer($enableCompilation);
            return $container;
        }
        return static::$builder->build();
    }

    public function initContainer($enableCompilation = true): Container
    {
        $needToResearchAttributes = $this->needToResearchAttributes();
        $needToRebuildContainer = $this->needToRebuildContainer();

        if ($needToResearchAttributes) {
            $this->searchAttributes();
        }
        require_once BASE_PATH.'/vendor/attributes.php';
        self::$builder = new \DI\ContainerBuilder();
        self::$builder->useAutowiring(false);
        if ($enableCompilation) {
            self::$builder->enableCompilation(SYSTEM_PATH.'/cnt_'.CONTAINER_NAME);
        }
        self::$builder->useAttributes(true);
        if ($needToRebuildContainer) {
            shell_exec('rm -rf '.SYSTEM_PATH.'/cnt_'.CONTAINER_NAME);
            $this->runLoaders(self::$builder);
        }
        self::$builder->addDefinitions([
            Container::class=>\DI\factory(function(ContainerInterface $c) {
                return $c;
            })
        ]);

        $container = self::$builder->build();
        if ($needToResearchAttributes || $needToRebuildContainer) {
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

    private function getTestsHashFromCache() {
        if (!file_exists(SYSTEM_PATH . '/hashes/tests_hash')) {
            @mkdir(SYSTEM_PATH . '/hashes', 0777, true);
            file_put_contents(SYSTEM_PATH . '/hashes/tests_hash', '10101010101010');
        }
        return file_get_contents(SYSTEM_PATH . '/hashes/tests_hash');
    }

    private function testsChanged(): bool {
        return $this->getTestsHashFromCache() !== $this->getHashOfAllMimeTypes(BASE_PATH.'/tests');
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

        $testsHash = $this->getHashOfAllMimeTypes(BASE_PATH.'/tests');
        file_put_contents(SYSTEM_PATH . '/hashes/tests_hash', $testsHash);

        $attributePathsHash = $this->collectHashOfAllAttributesPaths();
        file_put_contents(SYSTEM_PATH . '/hashes/attribute_hash', $attributePathsHash);
    }

    private function getHashOfAllMimeTypes(string $path, $hash = ''): string
    {
        if (!is_dir($path)) {
            return $hash;
        }
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

    private function devModeEnabledInDotEnvFile()
    {
        if (file_exists(BASE_PATH.'/.env')) {
            $env = file_get_contents(BASE_PATH.'/.env');
            $env = explode("\n", $env);
            foreach ($env as $line) {
                if (trim($line) === '') {
                    continue;
                }
                $exploded = explode('=', $line);
                if ($exploded[0] === 'DEV_MODE' && $exploded[1] === 'true') {
                    return true;
                }
            }
        }
        return false;
    }

    public function attributeScannablePaths() {
        if (file_exists(BASE_PATH.'/additional_paths.php')) {
            $additionalPaths = require_once BASE_PATH.'/additional_paths.php';
        }
        return array_merge([
            BASE_PATH.'/app',
            BASE_PATH.'/vendor/anodio',
            BASE_PATH.'/../protoPhp',
            BASE_PATH.'/vendor/symfony/routing/Attribute',
            BASE_PATH.'/vendor/symfony/event-dispatcher/Attribute'
        ], $additionalPaths ?? []);
    }

    public function getAttributeHashFromCache() {
        if (!file_exists(SYSTEM_PATH . '/hashes/attribute_hash')) {
            @mkdir(SYSTEM_PATH . '/hashes', 0777, true);
            file_put_contents(SYSTEM_PATH . '/hashes/attribute_hash', '10101010101010');
        }
        return file_get_contents(SYSTEM_PATH . '/hashes/attribute_hash');
    }

    public function collectHashOfAllAttributesPaths(): string {
        $hash = '';
        foreach ($this->attributeScannablePaths() as $include) {
            $hash = hash('xxh3', $hash.$this->getHashOfAllMimeTypes($include));
        }
        return $hash;
    }

    public function attributeScannablePathsChanged(): bool {
        return $this->getAttributeHashFromCache() !== $this->collectHashOfAllAttributesPaths();
    }

    public function excludeByRegExp(): ?string {
        return null;
    }

    private function searchAttributes()
    {
        $start = microtime(true);
        $datastore = new RuntimeDatastore();
        $datastore->set('allo', ['allo']);
        $io = new NullIO();
        $classMapGenerator = new MemoizeClassMapGenerator($datastore, $io);
        foreach ($this->attributeScannablePaths() as $include) {
            if (!is_dir($include)) {
                continue;
            }
            echo 'Scanning path: '.$include.PHP_EOL;
            $classMapGenerator->scanPaths($include, $this->excludeByRegExp());
        }


        $classMap = $classMapGenerator->getMap();
        $elapsed = microtime(true) - $start;
        echo "Scanned paths in $elapsed".PHP_EOL;

        $start = microtime(true);
        $classMapFilter = new MemoizeClassMapFilter($datastore, $io);
        $filter = new Chain([
            new ContentFilter(),
            new InterfaceFilter()
        ]);
        $classMap = $classMapFilter->filter(
            $classMap,
            fn (string $class, string $filepath): bool => $filter->filter($filepath, $class, $io)
        );
        $elapsed = microtime(true) - $start;
        echo "Generating attributes file: filtered class map in $elapsed".PHP_EOL;


        $start = microtime(true);
        $attributeCollector = new MemoizeAttributeCollector(new ClassAttributeCollector($io), $datastore, $io);
        $collection = $attributeCollector->collectAttributes($classMap);
        $elapsed = microtime(true) - $start;
        echo "Generating attributes file: collected attributes in $elapsed".PHP_EOL;

        $start = microtime(true);
        file_put_contents(BASE_PATH.'/vendor/attributes.php', TransientCollectionRenderer::render($collection));
        $elapsed = microtime(true) - $start;
        echo "Generating attributes file: rendered code in $elapsed".PHP_EOL;
    }

    private function recollectAttributesInDevMode()
    {
        if (file_exists(BASE_PATH.'/.env')) {
            $env = file_get_contents(BASE_PATH.'/.env');
            $env = explode("\n", $env);
            foreach ($env as $line) {
                if (trim($line) === '') {
                    continue;
                }
                $exploded = explode('=', $line);
                if ($exploded[0] === 'RECOLLECT_ATTRIBUTES_IN_DEV_MODE' && $exploded[1] === 'true') {
                    return true;
                }
            }
        }
        return false;
    }

    private function needToRebuildContainer(): bool
    {
        return $this->needToResearchAttributes();
    }

    public function needToResearchAttributes(): bool {
        return $this->devModeEnabledInDotEnvFile()
            || $this->attributeScannablePathsChanged()
            || $this->testsChanged()
            || $this->vendorChanged()
            || $this->appChanged()
            || $this->recollectAttributesInDevMode()
            || !file_exists(SYSTEM_PATH.'/cnt_'.CONTAINER_NAME);
    }
}
