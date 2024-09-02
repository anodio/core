<?php

namespace Anodio\Core\Loaders;

use Anodio\Core\AttributeInterfaces\LoaderInterface;
use Anodio\Core\Attributes\Command;
use Anodio\Core\Attributes\Loader;
use DI\ContainerBuilder;
use olvlvl\ComposerAttributeCollector\Attributes;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

#[Loader(priority: 110)]
class BasicsLoader implements LoaderInterface
{
    public function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            \Dotenv\Dotenv::class => function () {
                $dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
                $dotenv->safeLoad();
                return $dotenv;
            }
        ]);

        $targets = Attributes::findTargetClasses(Command::class);
        $commandClassesArray = [];
        foreach ($targets as $target) {
            $commandClassesArray[] = $target->name;
            $containerBuilder->addDefinitions([
                $target->name=>\Di\autowire()
                    ->method('setName', $target->attribute->name)
                    ->method('setDescription', $target->attribute->description)
                    ->method('setAliases', $target->attribute->aliases??[]),
            ]);
        }

        $containerBuilder->addDefinitions([
           'application'=>\Di\factory(function (ContainerInterface $c, array $targets) {
               $application = new Application();
                foreach ($targets as $target) {
                    $application->add($c->get($target));
                }
               return $application;
           })->parameter('targets', $commandClassesArray)
        ]);
    }
}
