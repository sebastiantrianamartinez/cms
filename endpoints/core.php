<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';
    
    $models = [
        "lib" => ["responser", "exception"]
    ];

    Routing::vendor();
    Routing::waf('waf');
    Routing::waf('auth/server');
    Routing::model(null, $models);

    $config = Routing::config('auth');
    

    $waf = new Waf($entityManager);
    $authServer = new AuthServer($entityManager);

    use Core\Database\Entities\Services;
    use Core\Database\Entities\Users;

    try{
        $data = json_decode(file_get_contents('php://input'), true);


        if(!isset($data) && $_SERVER["REQUEST_METHOD"] == 'POST'){
            $data = $_POST;
        }
        if(!isset($data) && $_SERVER["REQUEST_METHOD"] == 'GET'){
            $data = $_GET;
        }
    
        $serviceRepository = $entityManager->getRepository(Services::class);
        $service = $serviceRepository->find($sid);
    
        $sessionToken = $_COOKIE["session_token"]; 
        $user = null;
        $guest = false;
        
        if(isset($sessionToken)){
            $user = $authServer->authenticateTokenize($sessionToken);
        }
        else{
            $user = new Users();
            $user->setId();
            $user->setGroup($config["session"]["guest"]["group"]);
            $guest = true;
        }
    
        $action = 0;
        switch($_SERVER["REQUEST_METHOD"]){
            case 'POST':
                $action = 1;
                break;
            case 'GET':
                $action = 2;
                break;
            case 'PUT':
                $action = 4;
                break;
            case 'PATCH':
                $action = 4;
                break;
            case 'DELETE':
                $action = 8;
                break;
            default:
                $action = 15;
                break;
        }
    
        $api_key = '';
        $headers = apache_request_headers();
        if(isset($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            $api_key = $matches[1];
        } 
        else {
            $responser->toHttpRequest(401, "Invalid API KEY", null);
            die();
        }
        $authServer->authorize($user, $service, $api_key, $action) ;
    }
    catch(EnException $e){
        $responser = new Responser();
        $code = $e->getCode();
        switch($code){
            case 4012:
                Routing::view(null, 'error/blocked.php?reason= '.$e->getMessage() .' &until=' 
                .json_encode($e->getMetadata()), true);
                break;
            case 4008:
            case 4009:    
                $responser->toHttpRequest(401, "Invalid API KEY", null);
                break;
            case 4010:
                $responser->toHttpRequest(405, "Method not allowed, permission denied", null);
                break;
            case 4011:
                $responser->toHttpRequest(429, "You used this service recently, try it later", null);
                break;
            default:
                $responser->toHttpRequest(400, "Unknown error", null);
                break;
        }
        die();
    }

    
?>