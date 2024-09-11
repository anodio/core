<?php

namespace Anodio\Core\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\LogRecord;

class DioHandler extends StreamHandler
{
    protected $openedDio = null;

    public function close(): void
    {
        if ($this->openedDio !== null) {
            \dio_close($this->openedDio);
        }
        parent::close();
    }
    protected function write(LogRecord $record): void
    {
        if ($this->openedDio === null) {
            $this->openedDio = \dio_open($this->url, O_CREAT | O_APPEND | O_WRONLY, 0644);
        }
        \dio_write($this->openedDio, (string) $record->formatted);

    }
}
