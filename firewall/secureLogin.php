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
        ],
        "firewall" => "ruleController",
        "log" => "reporter"
    ];
    
    routing::bigRouting($modules);
    
    class secureLogin {
        private $database;
        private $ruleController;
        private $config;

        public function __construct(){
            $this->database = new database(true, NULL);
            $this->ruleController = new ruleController();
            $this->config = routing::config('firewall', 'sec_login');
        }
        public function failedAttempt(){
            $reporter = new reporter();
            $reporter->newLog(3, NULL, 1, NULL);
            $criticalAttempts = $this->config["data"];
            $queryRequest = (new queryBuilder())
                ->select('*')
                ->from('logs')
                ->where('log_class', '=', 3)
                ->and()
                ->where('log_ip', '=', $_SERVER["REMOTE_ADDR"])
                ->and()
                ->where('log_agent', '=', $_SERVER["HTTP_USER_AGENT"])
                ->and()
                ->where('log_iat', '>', (time() - (60*60*2)))
                ->build();
            $query = $queryRequest["query"];
            $params = $queryRequest["params"];
            $selectLogs = $this->database->executeQuery($query, $params)["data"]->fetchAll(PDO::FETCH_ASSOC);//["data"];
            if(empty(count($selectLogs)) || (!array_key_exists(count($selectLogs), $criticalAttempts))){
                foreach($criticalAttempts as $key => $value){
                    if($key > count($selectLogs)){
                        $left = $key - count($selectLogs);
                        break;
                    }
                    $left = $criticalAttempts[0] - count($selectLogs);
                }
                return responser::systemResponse(400, "Incorrect data " .$left ." attempts left", ["left" => $left]);
            }
            $this->ruleController->createRule(NULL, 2, 3, NULL, $criticalAttempts[count($selectLogs)], 1);
            $exp = ($criticalAttempts[count($selectLogs)] == -1) ? "permanently" : "temporarly";
            return responser::systemResponse(401, "User blocked " .$exp, ["exp" => time() + $criticalAttempts[count($selectLogs)]]);
        }
    }