<?php

namespace Bicycle\Core\AttributeInterfaces;

use DI\Attribute\Inject;

abstract class AbstractConfig
{
    public function __construct(array $data)
    {
        foreach ($data as $key=>$value) {
            $this->$key = $value;
        }
    }
}