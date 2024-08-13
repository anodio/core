<?php

namespace Bicycle\Core\AttributeInterfaces;

interface ConfigInterface
{
    public static function load(): static;
}