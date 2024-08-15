<?php

namespace Bicycle\Core\AttributeInterfaces;

use Psr\Log\LoggerInterface;

abstract class AbstractExceptionTrap
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function trap(\Throwable $exception): static
    {
        $this->report($exception);
        return $this;
    }

    abstract protected function report(\Throwable $exception): void;
}