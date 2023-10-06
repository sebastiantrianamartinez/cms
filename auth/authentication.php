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

    routing::bigRouting($modules);

    class authentication {
        private $jwtController;
        private $database;
        private $qb;
        private $config;

        public function __construct(){
            $this->database = new database(true, NULL);
            $this->qb = new queryBuilder();
            $this->jwtController = new jwtController();
            $this->config = routing::config('auth', NULL)["data"];
        }

        public function newSession($userId, $extends){
            $this->qb = new queryBuilder();
            $queryRequest = $this->qb->select('*')
                ->from('users')
                ->where('user_id', '=', $userId)
                ->build();
            $selectQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            //return ["query" => $selectQuery, "params" => $params];
            $userRequest = $this->database->executeQuery($selectQuery, $params)["data"];
            $userRequest = $userRequest->fetch();
            
            $exp = time();
            $offset = $this->config["offsets"]["lamda"];
            $preffix = $this->config["preffix"];

            if($userRequest["user_id"] == $userId){
                $exp += ($userRequest["user_group"] <= 5) ? $this->config["timing"]["admin"] : $this->config["timing"]["user"];
                $payload = [
                    "iat" => time(),
                    "exp" => $exp,
                    "id" => $userRequest["user_id"] + $offset,
                    "name" => $userRequest["user_alias"],
                    "group" => $userRequest["user_group"]
                ];
                $token = $this->jwtController->newToken($payload, NULL, NULL)["data"];
                $tokenDbData = [
                    "token_value" => $token,
                    "token_iat" => time(),
                    "token_exp" => $exp,
                    "token_user" => $userId,
                    "token_ip" => $_SERVER['REMOTE_ADDR'],
                    "token_agent" => $_SERVER['HTTP_USER_AGENT'],
                    "token_ping" => time()
                ];
                $this->qb = new queryBuilder();
                $queryRequest = $this->qb->insert('tokens', $tokenDbData)->build();
                $insertQuery = $queryRequest["query"];
                $params = $queryRequest["params"];
                $tokenRequest = $this->database->executeQuery($insertQuery, $params);
                $exp = ($extends == true) ? $exp : NULL;
                setcookie($preffix, $token, $exp, '/', NULL); //true, true); SECURE OPTIONS
                return responser::systemResponse(200, "Session stablished", $token);
            }
            return responser::systemResponse(400, "User not found in database", $token);
        }
        
        public function readSession(){
            $preffix = $this->config["preffix"];
            if(!isset($_COOKIE[$preffix])){
                return responser::systemResponse(400, "Token not found: " .$preffix, 100);
            }
            $token = $_COOKIE[$preffix];

            $decodeRequest = $this->jwtController->decodeToken($token, NULL, NULL);
            if($decodeRequest["status"] != 200){
                if($decodeRequest["data"]["expired"]){
                    return responser::systemResponse(400, "Token expired", 100);
                }
                return responser::systemResponse(400, "Token malformed", $decodeRequest);
            }
            $this->qb = new queryBuilder();
            $queryRequest = $this->qb->select('*')
                ->from('tokens')
                ->where('token_value', '=', $token)
                ->build();
            $selectQuery = $queryRequest["query"];
            $params = $queryRequest["params"];
            $tokenRequest = $this->database->executeQuery($selectQuery, $params)["data"];
            $tokenRequest = $tokenRequest->fetch();
            if(!is_int($tokenRequest["token_id"])){
                return responser::systemResponse(400, "Token doesn't exists in database", 300);
            }
            $tokenRequest["data"]["user"] -= $this->config["offsets"]["lamda"];
            return responser::systemResponse(200, "Session stablished", ["payload" => $decodeRequest["data"], "token_id" => $tokenRequest["token_id"]]);
        }

        public function deleteSession(){
            $sessionRequest = $this->readSession();
            if($sessionRequest["status"] == 200){
                $preffix = $this->config["preffix"];
                $this->qb = new queryBuilder();
                $queryRequest = $this->qb->where('token_id', '=', $sessionRequest["data"]["token_id"])
                    ->delete('tokens')
                    ->build();
                $deleteQuery = $queryRequest["query"];
                $params = $queryRequest["params"];
                $tokenRequest = $this->database->executeQuery($deleteQuery, $params);
                setcookie($preffix, "", -1000, '/', NULL); //true, true); SECURE OPTIONS
                return responser::systemResponse(200, "Session deleted ", NULL);
            }
            return responser::systemResponse(400, "No sessions for destroy " .json_encode($sessionRequest), 100);
        }

        public function updateSession() {
            $preffix = $this->config["preffix"];
            
            // Verificar si la cookie con el token existe
            if (!isset($_COOKIE[$preffix])) {
                return responser::systemResponse(400, "Token not found: " . $preffix, 100);
            }
            
            $token = $_COOKIE[$preffix];
            
            // Decodificar el token actual
            $decodeRequest = $this->jwtController->decodeToken($token, NULL, NULL);
            
            if ($decodeRequest["status"] != 200) {
                return responser::systemResponse(400, "Token malformed", $decodeRequest);
            }
            
            // Obtener la información del token decodificado
            $payload = $decodeRequest["data"];
            
            // Verificar si la fecha de expiración está a 5 días o menos en el futuro
            $currentTimestamp = time();
            $expirationTimestamp = $payload["exp"];
            $fiveDaysInSeconds = 5 * 24 * 60 * 60; // 5 días en segundos
            
            if ($expirationTimestamp - $currentTimestamp <= $fiveDaysInSeconds) {
                // Generar un nuevo token con una nueva fecha de expiración
                $newExpirationTimestamp = $currentTimestamp + $fiveDaysInSeconds;
                $payload["exp"] = $newExpirationTimestamp;
                $newToken = $this->jwtController->newToken($payload, NULL, NULL)["data"];
                $updateData = [
                    'token_value' => $newToken,
                    'token_exp' => $newExpirationTimestamp,
                ];
                // Actualizar el token en la base de datos
                $queryRequest = $this->qb->update('tokens', $updateData)
                    ->where('token_value', '=', $token)
                    ->build();
                $updateQuery = $queryRequest["query"];
                $params = $queryRequest["params"];
                $tokenRequest = $this->database->executeQuery($updateQuery, $params);
                setcookie($preffix, $newToken, $newExpirationTimestamp, '/', NULL); //true, true); SECURE OPTIONS
            
            }
            
            return responser::systemResponse(200, "Session updated", $newToken);
        }
    }