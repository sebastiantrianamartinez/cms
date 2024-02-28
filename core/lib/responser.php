<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $models = [
        'lib' => 'exception'
    ];

    interface responserInterface {
        public static function toSystem(int $status, string $message = null, $data);
        public static function toHttpRequest(int $status, string $message = null, $data);
        public static function preHttpRequest(array $response);
        public static function exception(string $message, int $code, $previous, $metadata);
    }

    class Responser implements responserInterface {
        
        public static function toSystem(int $status, string $message = null, $data){
            $response = [
                "status" => $status,
                "message" => $message,
                "data" => $data,
                "signature" => 'SYSTEM:RESPONSE:AT:' .time()
            ];
            return $response;
        }

        public static function toHttpRequest(int $status, string $message = null, $data){
            $response = [
                "status" => $status,
                "message" => $message,
                "data" => $data,
                "signature" => 'HTTP:RESPONSE:AT:' .time()
            ];
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($status);
            echo json_encode($response);
        }

        public static function preHttpRequest(array $response){
            $response["signature"] = 'HTTP:RESPONSE:AT:' .time();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($responser["code"]);
            echo json_encode($response);
        }

        public static function exception(string $message, int $code, $previous, $metadata){
            $EnException = new EnException($message, $code, $previous, $metadata);
            throw $EnException;
        }

    }

?>