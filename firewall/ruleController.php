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
    ];

    class ruleController{

        public function __construct() {
            $this->database = new database(true, NULL);
        }

        public function createRule($userId, $ruleCode, $ruleCause, $ruleMessage, $ruleExp, $ruleService) {
            $iat = time();
            $agent = $_SERVER['HTTP_USER_AGENT'];
            $ip = $_SERVER['REMOTE_ADDR'];
        
            $data = [
                'rule_user' => $userId,
                'rule_code' => $ruleCode,
                'rule_cause' => $ruleCause,
                'rule_message' => $ruleMessage,
                'rule_service' => $ruleService,
                'rule_iat' => $iat,
                'rule_agent' => $agent,
                'rule_exp' => ($ruleExp != -1) ? time() + $ruleExp : NULL,
                'rule_ip' => $ip
            ];
            $this->qb = new queryBuilder();
            $queryRequest = $this->qb->insert('rules', $data)->build();
            $insertQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $ruleRequest = $this->database->executeQuery($insertQuery, $params);
        
            if ($ruleRequest["status"] === 200) {
                return responser::systemResponse(200, "Regla creada correctamente", $data);
            } else {
                return responser::systemResponse(400, "Error al crear la regla", NULL);
            }
        }

        public function getRules($userId = null, $service = null, $cause = null, $appendUserAgentAndIP = false) {
            $query = new queryBuilder();
            $agent = $_SERVER['HTTP_USER_AGENT'];
            $ip = $_SERVER['REMOTE_ADDR'];
        
            $query->select('*')->from('rules');
            $query->where('1', '=', '1');
        
            if ($userId !== null) {
                $query->where('rule_user', '=', $userId);
            }
        
            if ($service !== null) {
                $query->and()->where('rule_service', '=', $service);
            }
        
            if ($cause !== null) {
                $query->and()->where('rule_cause', '=', $cause);
            }
        
            if ($appendUserAgentAndIP) {
                $query->and()->where('rule_ip', '=', $ip)->and(); // Usar AND para agregar condiciones de usuario e IP
                $query->where('rule_agent', '=', $agent);
            }
        
            $query->and()->groupOpen()
                ->where('rule_exp', '>', time())
                ->orWhere('rule_exp', 'IS', null) // Cambiado '=' por 'IS' para NULL
                ->groupClose();
        
            $queryRequest = $query->build();
            $selectQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $rulesRequest = $this->database->executeQuery($selectQuery, $params);
        
            if ($rulesRequest["status"] === 200) {
                $rules = $rulesRequest["data"]->fetchAll(PDO::FETCH_ASSOC);
                if(empty($rules)){
                    return responser::systemResponse(204, "Success request, no coincidences", NULL);
                }
                return responser::systemResponse(200, "Sucess request, some coincidences", $rules);
            } else {
                return responser::systemResponse(400, "Error requesting rules", $selectQuery);
            }
        }
        
    }