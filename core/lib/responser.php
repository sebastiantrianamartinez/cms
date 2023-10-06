<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";

    class responser {
        public static function systemResponse(int $status, string $message, $data){
            return [
                "status" => $status,
                "message" => $message,
                "data" => $data,
                "signature" => "SYSTEM::RESPONSE::AT::" .time()
            ];
        }
        public static function preformedHttpResponse($objectResposne){
            $objectResposne["signature"] = "HTTP::RESPONSE::AT::" .time();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($objectResposne["status"]);
            echo json_encode($objectResposne);
        }
    
        public static function httpResponse($code, $message, $data){
            $response = [
                "status" => $code,
                "message" => $message,
                "data" => $data,
                "signature" => "HTTP::RESPONSE::AT::" .time()
            ];
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($code);
            echo json_encode($response);
        }

    }