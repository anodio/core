<?php

namespace Bicycle\Core\Configuration;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EnvRequired
{
    public function __construct(
        public string $name
    ) {
    }
}