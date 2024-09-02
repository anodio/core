<?php

namespace AnodioCoreTests\Support\SampleConfigs;

use Anodio\Core\AttributeInterfaces\AbstractConfig;
use Anodio\Core\Attributes\Config;
use Anodio\Core\Configuration\Env;

#[Config('sample')]
class SampleConfig extends AbstractConfig
{
    #[Env('STRING_VAR', default: 'default')]
    public string $stringVar;

    #[Env('BOOL_VAR', default: false)]
    public bool $boolVar;
}
