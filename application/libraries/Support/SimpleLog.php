<?php
namespace Lib\Support;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SimpleLog
{
    public static function log($path = '/', $data, $level = Logger::INFO)
    {
        static $log = null;

        if ($log === null) {
            $log = new Logger('logs');
        }

        $log->pushHandler(new StreamHandler('logs/' . $path . '_' . date('Ymd') . '.log', $level));
        (is_array($data) || is_object($data)) && $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $log->addRecord($level, $data);
        $log->popHandler();
    }

    /**
     * @param string $path
     * @param \Exception $e
     * @param int $level
     */
    public static function logException($path = '/', $e, $level = Logger::INFO)
    {
        static $log = null;

        if ($log === null) {
            $log = new Logger('logs');
            //$log->pushHandler(new StreamHandler(APPLICATION_PATH . 'logs/' . $path . '.log', $level));
        }

        $log->pushHandler(new StreamHandler(APPLICATION_PATH . 'logs/' . $path . '_' . date('Ymd') . '.log', $level));

        $data = $e->getFile() . '(' . $e->getLine() . ')' . ' ' . $e->getMessage();

        $log->addRecord($level, $data);
        $log->popHandler();
    }
}