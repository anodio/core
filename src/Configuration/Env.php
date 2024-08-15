<?php

namespace Bicycle\Core\Configuration;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Env
{
    public function __construct(
        public string $name,
        public mixed $default = null
    ) {
    }
}