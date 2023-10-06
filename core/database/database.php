<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/routing/routing.php';
    $modules = [
        "excp" => "throwing",
        "lib" => "responser",
        "database" => "queryBuilder"
    ];
    
    routing::bigRouting($modules);

    class database {
        private $databaseConfig;
        private $databaseKey;
        private $databaseDsn;
        private $databaseSettings;
        private $databaseConnection;

        public function __construct(bool $isLocal, $dsn){
            $this->databaseConfig = routing::config('database', NULL)["data"];
            $this->databaseKey = routing::key('database')["data"];
            $this->databaseDsn = $this->databaseConfig["default"];
            $this->databaseSettings = $this->databaseConfig["settings"];

            try {
                $this->databaseConnection = new PDO(
                    "mysql:host=" .$this->databaseDsn["host"] .';dbname=' .$this->databaseDsn["name"], 
                    $this->databaseDsn["user"], 
                    $this->databaseKey, 
                    $this->databaseSettings
                );
            } catch (PDOException $e) {
                throwing::sendLog('database', 3, $e);
            }
        }

        public function getConnection(){
            if($this->databaseConnection instanceof PDO){
                return responser::systemResponse(200, "Connection stablished", $this->databaseConnection);
            } 
            else{
                return responser::systemResponse(400, "Connection error", NULL);
            }
        }
        
        public function executeQuery($query, $params) {
            try {
                $result = $this->databaseConnection->prepare($query);
                $result->execute($params);
                return responser::systemResponse(200, "success database query", $result);
            } catch (PDOException $e) {
                throwing::sendLog('database', 3, $e .' query: ' .$query);
                return responser::systemResponse(400, "database error", (string)$e );
            }
        }
    }