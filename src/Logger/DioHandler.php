<?php

namespace Anodio\Core\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\LogRecord;

class DioHandler extends StreamHandler
{

    protected function write(LogRecord $record): void
    {
        // Flags from error_log php sources (php_log_err_with_severity)
        $fp = \dio_open($this->url, O_CREAT | O_APPEND | O_WRONLY, 0644);
        \dio_write($fp, (string) $record->formatted );
        \dio_close($fp);
    }
}
