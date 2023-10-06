<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/routing/routing.php';
    require_once ROOT .'/vendor/autoload.php';

    $modules = [
        "lib" => "responser"
    ];

    routing::bigRouting($modules);

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class jwtController {

        private $jwt_key;
        private $api_key;

        public function __construct(){
            $this->jwt_key = routing::key('jwt')["data"];
            $this->api_key = routing::key('api')["data"];
        }

        public function newToken($payload, $key, $method){
            $key = ($key !== NULL) ? $key : $this->jwt_key;
            if($key==="api_key"){
                $key = $this->api_key;
            }
            $method = ($method != NULL) ? $method : "HS256";
            $token = JWT::encode($payload, $key, $method);
            return responser::systemResponse(200, "Token created", $token);
        }

        public function decodeToken($token, $key, $method){
            if(!is_string($token)){
                return responser::systemResponse(400, "Invalid token data", NULL);
            }
            $key = ($key !== NULL) ? $key : $this->jwt_key;
            $method = ($method != NULL) ? $method : "HS256";
            if($key === "api_key"){
                $key = $this->api_key;
            }
            try {
                $payload = JWT::decode($token, new Key($key, $method));
        
                // Convertir el objeto stdClass en un arreglo asociativo sin malformar las cadenas
                $payloadArray = $this->stdClassToArray($payload);
        
                return responser::systemResponse(200, "Valid and decoded token", $payloadArray);
            } catch (Exception $e) {
                return responser::systemResponse(400, "Token decoded failed", ["excp" => (string)$e, "expired" => (bool)strpos($e, 'expired')]);
            }
        }
        
        // Función personalizada para convertir stdClass en un arreglo asociativo sin malformar las cadenas
        private function stdClassToArray($stdClass) {
            $array = [];
            foreach ($stdClass as $key => $value) {
                if (is_object($value)) {
                    $array[$key] = $this->stdClassToArray($value); // Recursión para objetos anidados
                } elseif (is_string($value)) {
                    $array[$key] = $value; // Mantener las cadenas sin cambios
                } else {
                    $array[$key] = $value;
                }
            }
            return $array;
        }
        
    }
?>
