<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT . '/core/routing/routing.php';
    
    $timezone = routing::config('project', 'dns')["data"]["timezone"];
    date_default_timezone_set($timezone);
    
    $modules = [
        "lib" => "responser",
        "database"=> [
            "queryBuilder",
            "database"
        ]
    ];

    class reporter {
        private $database;
        public function __construct(){
            $this->database = new database(true, NULL);
        }

        public function newLog($class, $user = NULL, $service, $message = NULL){
            $qb = new queryBuilder();
            $logData = [
                "log_class" => $class,
                "log_user" => $user,
                "log_ip" => $_SERVER['REMOTE_ADDR'],
                "log_agent" => $_SERVER['HTTP_USER_AGENT'],
                "log_iat" => time(),
                "log_ping" => time(),
                "log_service" => $service,
                "log_message" => $message,
                "log_url" => $_SERVER['REQUEST_URI']
            ];
            $queryRequest = $qb->insert('logs', $logData)->build();
            $insertQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $this->database->executeQuery($insertQuery, $params);
        }
    }