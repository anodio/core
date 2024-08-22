<?php

namespace Anodio\Core\Config;

use Anodio\Core\Configuration\Env;
use Monolog\Handler\StreamHandler;

class LoggerConfig
{
    #[Env('LOG_HANDLER', default: StreamHandler::class)]
    public string $logHandler;

    #[Env('LOG_FORMATTER', default: 'Monolog\Formatter\JsonFormatter')]
    public string $logFormatter;

    #[Env('LOG_LEVEL', default: 'DEBUG')]
    public string $logLevel;

    #[Env('LOG_DESTINATION', default: 'php://stdout')]
    public string $logDestination = 'php://stdout';
}