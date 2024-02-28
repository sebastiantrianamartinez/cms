<?php 
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/routing/routing.php';
    require_once ROOT .'/vendor/autoload.php';

    $models = [
        "lib" => ["exception"]
    ];
    Routing::model(null, $models, 'r_o');

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    class Tokenization {
        private $jwt_key;
        private $api_key;

        public function __construct($key = null){
            $key = ($key !== null) ? $key : 'jwt';
            $this->jwt_key = Routing::key($key);
        }

        public function newToken($payload, $key = null, $method = null){
            $key = ($key !== NULL) ? $key : $this->jwt_key;
            $method = ($method !== NULL) ? $method : "HS256";
            $token = JWT::encode($payload, $key, $method);
            return $token;
        }

        public function decodeToken($token, $key = null, $method = null){
            if(!is_string($token)){
                throw new EnException("Invalid token data", 400, null, null);
            }
            $key = ($key !== NULL) ? $key : $this->jwt_key;
            $method = ($method != NULL) ? $method : "HS256";
            if($key === "api_key"){
                $key = $this->api_key;
            }
            try {
                $payload = JWT::decode($token, new Key($key, $method));
                $payloadArray = $this->stdClassToArray($payload);
                return $payloadArray; 
            } catch (Exception $e) {
                $metadata = ["excp" => (string)$e, "expired" => (bool)strpos($e, 'expired')];
                throw new EnException("Token decoded failed", 400, null, $metadata);
            }
        }
        
        private function stdClassToArray($stdClass) {
            $array = [];
            foreach ($stdClass as $key => $value) {
                if (is_object($value)) {
                    $array[$key] = $this->stdClassToArray($value); 
                } elseif (is_string($value)) {
                    $array[$key] = $value; 
                } else {
                    $array[$key] = $value;
                }
            }
            return $array;
        }
        
    }