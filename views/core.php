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
    
    $entityManager = Routing::entityManager();

    $waf = new Waf($entityManager);
    $authServer = new AuthServer($entityManager);

    use Core\Database\Entities\Services;
    use Core\Database\Entities\Users;

    try{
        $serviceRepository = $entityManager->getRepository(Services::class);
        $service = $serviceRepository->find($sid);
    
        $sessionToken = $_COOKIE["session_token"]; 
        $user = null;
        $guest = false;
        
        if(isset($sessionToken)){
            $user = $authServer->authenticateTokenize($sessionToken);
            if(!$user){
                $guest = true;
                setcookie("session_token", "", time() - 3600, "/");
            }
        }
        if($guest || !$user){
            $user = new Users();
            $user->setId();
            $user->setGroup($config["session"]["guest"]["group"]);
            $guest = true;
        }
    
        $api_key = $authServer->authorizationTokenize($user, $service); 
        $api_keys[$sid] = $api_key; 
    }
    catch(EnException $e){
        $responser = new Responser();
        $code = $e->getCode();
        switch($code){
            case 4004:
            case 4005:    
                Routing::view(null, 'error/unauthorized.php', true);
                break;
            default:
                $responser->toHttpRequest(400, "Unknown error", null);
                break;
        }
    }

    
?>