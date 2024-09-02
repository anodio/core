<?php

namespace AnodioCoreTests\Loaders;

use Anodio\Core\Loaders\BasicsLoader;
use Anodio\Core\Loaders\ConfigLoader;
use AnodioCoreTests\Support\SampleConfigs\SampleConfig;
use DI\ContainerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;

class ConfigLoaderTest extends \AnodioCoreTests\TestCase
{
    public static function configDataProvider()
    {
        return [
            [
                'stringVar' => 'string',
                'expectedStringVar' => 'string',
                'boolVar' => true,
                'expectedBoolVar' => true
            ],
            [
                'stringVar' => '',
                'expectedStringVar' => '',
                'boolVar' => 'true',
                'expectedBoolVar' => true
            ],
            [
                'stringVar' => 'string',
                'expectedStringVar' => 'string',
                'boolVar' => false,
                'expectedBoolVar' => false
            ],
            [
                'stringVar' => 'string',
                'expectedStringVar' => 'string',
                'boolVar' => 'false',
                'expectedBoolVar' => false
            ],
        ];
    }

    #[DataProvider('configDataProvider')]
    public function testConfigLoaders($stringVar, $boolVar, $expectedStringVar, $expectedBoolVar)
    {
        $_ENV['STRING_VAR'] = $stringVar;
        $_ENV['BOOL_VAR'] = $boolVar;
        $containerBuilder = new ContainerBuilder();

        $basicsLoader = new BasicsLoader();
        $basicsLoader->load($containerBuilder);
        $loader = new ConfigLoader();
        $loader->load($containerBuilder);
        $container = $containerBuilder->build();

        $sampleConfig = $container->get(SampleConfig::class);
        $this->assertSame($expectedStringVar, $sampleConfig->stringVar);
        $this->assertSame($expectedBoolVar, $sampleConfig->boolVar);

    }
}
