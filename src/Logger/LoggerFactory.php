<?php

namespace Anodio\Core\Logger;

use Anodio\Core\Config\LoggerConfig;
use DI\Attribute\Inject;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerFactory
{

    #[Inject]
    public LoggerConfig $config;

    public function createLogger() {
        $logger = new Logger('Logger');
        if (empty($this->config->logDestination)) {
            $logDestination = 'php://stdout';
        } else {
            $logDestination = $this->config->logDestination;
        }
        if (empty($this->config->logLevel)) {
            $logLevel = \Monolog\Level::Debug;
        } else {
            $logLevel = $this->config->logLevel;
        }
        if (empty($this->config->logFormatter)) {
            $logFormatter = new \Monolog\Formatter\JsonFormatter();
        } else {
            $logFormatter = new $this->config->logFormatter();
        }
        if (empty($this->config->logHandler)) {
            $handler = new StreamHandler('php://stdout', $logLevel);
        } else {
            /**
             * @var FormattableHandlerInterface $handler
             */
            if ($this->config->logHandler==='\\Monolog\\Handler\\NullHandler') {
                $handler = new $this->config->logHandler($logLevel);
            } else {
                $handler = new $this->config->logHandler($logDestination, $logLevel);
                $handler->setFormatter($logFormatter);
            }
        }
        $logger->pushHandler($handler);
        return $logger;
    }
}
