<?php

enum LogLevel {
    case INFO;
    case WARN;
    case ERROR;
}

class Logger
{
    private static $logFile = __DIR__ . 'logs/backend.log';

    public static function log(LogLevel $level, string $message) : void {
        if (!file_exists(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0755, true);
        }

        $time = date('Y-m-d H:i:s');
        $entry = "[$time] [$level->name] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }
}
