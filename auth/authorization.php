<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $timezone = routing::config('project', 'dns')["data"]["timezone"];
    date_default_timezone_set($timezone);

    $modules = [
        "database"=> [
            "queryBuilder",
            "database"
        ],
        "tokenization" => "jwtController",
        "lib" => "responser",
        "firewall" => [
            "middleware",
            "ruleController"
        ]
    ];

    routing::bigRouting($modules);

    class authorization {
        private $jwtController;
        private $database;
        private $qb;
        private $config;
        private $ruleController;
        private $middleware;

        public function __construct(){
            $this->database = new database(true, NULL);
            $this->qb = new queryBuilder();
            $this->jwtController = new jwtController();
            $this->config = routing::config('auth', NULL)["data"];
            $this->ruleController = new ruleController();
            $this->middleare = new middleware();
        }

        public function permission(array $user, array $service){
            $this->qb = new queryBuilder();
            $queryRequest = $this->qb->select('*')
                ->from('permissions')
                ->where('permission_service', '=', $service['id'])
                ->and()
                ->groupOpen()  // Abre un grupo de condiciones ( para OR
                    ->where('permission_user', '=', $user['id'])
                    ->or()
                    ->where('permission_group', '=', $user['group'])
                    ->or()
                    ->where('permission_group', '=', -1)
                ->groupClose()  // Cierra el grupo de condiciones )
                ->build();
            $selectQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $permissionRequest = $this->database->executeQuery($selectQuery, $params)["data"];
            $permissionRequest = $permissionRequest->fetch();
            if(empty($permissionRequest)){
                return responser::systemResponse(401, "Access denied to service", 1);
            }
            $this->qb = new queryBuilder();
            $queryRequest = $this->qb->select('*')
                ->from('services')
                ->where('service_id', '=', $service['id'])
                ->build();
            $selectQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $serviceRequest = $this->database->executeQuery($selectQuery, $params)["data"];
            $serviceRequest = $serviceRequest->fetch();
            
            $exp = time();
            $exp += ($serviceRequest["service_deadline"] !== NULL) ? $serviceRequest["service_deadline"] : $this->config["api_timing"];

            $payload = [
                "iat" => time(), 
                "exp" => $exp,
                "id" => $serviceRequest["service_id"],
                "client" => $user["id"] + $this->config["offsets"]["lamda"],
                "key" => password_hash($serviceRequest["service_key"], PASSWORD_BCRYPT)
            ];

            $api_key = $this->jwtController->newToken($payload, "api_key", NULL)["data"]; 
            $userId = ($user["id"] == 0) ? NULL : $user["id"];
            $ruleRequest = $this->ruleController->getRules($userId, $service["id"], NULL, true);
            if($ruleRequest["status"] == 200){
                return responser::systemResponse(403, "Access denied to service, user blocked", 2);
            }
            return responser::systemResponse(200, "Acces granted ", $api_key);
        }

        public function transaction(array $user, array $service, string $api_key){
            
            $api_keyRequest = $this->jwtController->decodeToken($api_key, "api_key", NULL);
            if($api_keyRequest["status"] !== 200){
                $master_api_key = routing::key('master_api');
                if($master_api_key["status"] == 200){
                    if($master_api_key["data"] != $api_key){
                        return responser::systemResponse(401, "Transaction denied, api_key is wrong ", 3);
                    } 
                }
                else{
                    return responser::systemResponse(401, "Transaction denied, api_key is wrong", 3);
                }
                return responser::systemResponse(200, "Transaction approved", NULL);
            }
            $api_key = $api_keyRequest["data"]["key"];
            $this->qb = new queryBuilder();
            $queryRequest = $this->qb->select('*')
                ->from('services')
                ->where('service_id', '=', $service['id'])
                ->build();
            $selectQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $serviceRequest = $this->database->executeQuery($selectQuery, $params)["data"];
            $serviceRequest = $serviceRequest->fetch();

            $service_key = $serviceRequest["service_key"];
            if(!password_verify($service_key, $api_key)){
                return responser::systemResponse(401, "Transaction denied, service_key is wrong ", 4);
            }
            if(is_int($serviceRequest["service_timeout"])){
                $this->qb = new queryBuilder();
                $timeout = time() - $serviceRequest["service_timeout"];
                $queryRequest = $this->qb->select('*')
                    ->from('logs')
                    ->where('log_user', '=', $user["id"])
                    ->and()
                    ->where('log_service', '=', $service["id"])
                    ->and()
                    ->where('log_iat', '>', $timeout)
                    ->build();
                $selectQuery = $queryRequest["query"];
                $params = $queryRequest["params"];
                $logRequest = $this->database->executeQuery($selectQuery, $params)["data"];
                $logRequest = $logRequest->fetch();
                if(!empty($logRequest)){
                    return responser::systemResponse(401, "Transaction denied, recent transaction log", 5);
                }
            }
            $userId = ($user["id"] == 0) ? NULL : $user["id"];
            $ruleRequest = $this->ruleController->getRules($userId, $service["id"], NULL, true);
            if($ruleRequest["status"] == 200){
                return responser::systemResponse(403, "Access denied to service, user blocked", 2);
            }
            return responser::systemResponse(200, "Transaction approved", NULL);
        }
    }
