<?php


namespace Anodio\Core\Logger;

use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;

class LogContextProcessor implements ProcessorInterface
{
    private \Monolog\Level $level;
    /** @var array{branch: string, commit: string}|array<never>|null */
    private static $cache = null;

    public function __construct(int|string|\Monolog\Level $level = \Monolog\Level::Debug)
    {
        $this->level = Logger::toMonologLevel($level);
    }

    public function __invoke(\Monolog\LogRecord $record)
    {
        foreach (Context::get() as $key => $value) {
            $record->extra[$key] = $value;
        }
        return $record;
    }
}
