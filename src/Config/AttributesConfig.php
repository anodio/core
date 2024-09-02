<?php

namespace Anodio\Core\Config;

use Anodio\Core\AttributeInterfaces\AbstractConfig;
use Anodio\Core\Attributes\Config;
use Anodio\Core\Configuration\Env;

#[Config('attributes')]
class AttributesConfig extends AbstractConfig
{
    #[Env('RECOLLECT_ATTRIBUTES_IN_DEV_MODE', false)]
    public string $recollectAttributesInDevMode;
}
