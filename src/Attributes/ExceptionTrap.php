<?php

namespace Bicycle\Core\Attributes;

use Bicycle\Core\Abstraction\AbstractAttribute;
use DI\ContainerBuilder;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ExceptionTrap extends AbstractAttribute
{
    private ContainerBuilder $containerBuilder;
    /**
     * @var string
     */
    private string $loggerName;

    public function __construct(string $loggerName)
    {
        $this->loggerName = $loggerName;
    }

    public function setContainerBuilder(ContainerBuilder $containerBuilder): static
    {
        $this->containerBuilder = $containerBuilder;
        return $this;
    }
    public function onClass(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);
        if (!$reflectionClass->isSubclassOf('Bicycle\Core\AttributeInterfaces\AbstractExceptionTrap')) {
            throw new \Exception('The class ' . $className . ' must extend Bicycle\\Core\\AttributeInterfaces\\AbstractExceptionTrap');
        }
        $this->containerBuilder->addDefinitions([
            $className => \DI\create($className)->constructor(\DI\get($this->loggerName))
        ]);
        return true;
    }
}