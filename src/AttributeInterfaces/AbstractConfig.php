<?php

namespace Anodio\Core\AttributeInterfaces;

use DI\Attribute\Inject;
use Dotenv\Dotenv;

abstract class AbstractConfig
{

    #[Inject]
    protected Dotenv $dotenv;

    public function __construct(array $data = [])
    {
        foreach ($data as $key=>$value) {
            if (trim($value)==='false' || $value===false) {
                $this->$key = false;
            } elseif (trim($value)==='true' || $value===true) {
                $this->$key = true;
            } else {
                $this->$key = $value;
            }
        }
    }
}
