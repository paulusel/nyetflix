<?php
// includes/logger.php

class Logger
{
    private static $logFile = __DIR__ . '/../logs/app.log';

    public static function log($level, $message)
    {
        if (!file_exists(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0777, true);
        }

        $time = date('Y-m-d H:i:s');
        $entry = "[$time] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }

    public static function info($message)
    {
        self::log('INFO', $message);
    }

    public static function warning($message)
    {
        self::log('WARNING', $message);
    }

    public static function error($message)
    {
        self::log('ERROR', $message);
    }
}
?>