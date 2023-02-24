<?php

class Logger
{

    /**
     *--------------------------------------------------------------------------
     * Log
     *--------------------------------------------------------------------------
     *
     * @param string $text
     * @return 
     */

    public function log($text)
    {
        file_put_contents('./src/utility_classes/log.txt', $text . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
