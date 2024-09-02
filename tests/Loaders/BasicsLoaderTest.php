<?php

namespace AnodioCoreTests\Loaders;

use Anodio\Core\Commands\PrintAllEnvsCommand;
use Anodio\Core\Loaders\BasicsLoader;
use DI\ContainerBuilder;

class BasicsLoaderTest extends \AnodioCoreTests\TestCase
{
    public function testBasicsLoaders()
    {
        $loader = new BasicsLoader();
        $containerBuilder = new ContainerBuilder();
        $loader->load($containerBuilder);
        $container = $containerBuilder->build();

        $this->assertInstanceOf(\Dotenv\Dotenv::class, $container->get(\Dotenv\Dotenv::class));

        $printEnvCommand = $container->get(PrintAllEnvsCommand::class);
        $this->assertSame('env:print-all', $printEnvCommand->getName());
        $this->assertSame('Print all possible envs', $printEnvCommand->getDescription());

        $application = $container->get('application');
        $this->assertInstanceOf(\Symfony\Component\Console\Application::class, $application);
    }
}
