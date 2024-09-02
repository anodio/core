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
            if ($value=='false') {
                $this->$key = false;
            } elseif ($value=='true') {
                $this->$key = true;
            } else {
                $this->$key = $value;
            }
        }
    }
}
