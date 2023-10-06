<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    date_default_timezone_set('America/Bogota');

    class throwing{
        public static function sendLog(string $sender, int $damage, $exception){
            $logFolder = ROOT .'/nosql/log/';
            $levels = ["warning", "error", "hotfix"];
            $logFile = $logFolder .$levels[$damage - 1] .'.log';
            $logText = '[SYSTEM ERROR AT ' .date('d-m-Y H:i:s') .'] [BY: ' .$sender .'] =>'
            .(string)$exception ."\n";
            file_put_contents($logFile, $logText, FILE_APPEND);
        }
    }