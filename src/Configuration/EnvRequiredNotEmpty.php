<?php

namespace Bicycle\Core\Configuration;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EnvRequiredNotEmpty
{
    public function __construct(
        public string $name
    ) {
    }
}