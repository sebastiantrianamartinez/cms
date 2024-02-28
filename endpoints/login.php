<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';
    
    $models = [
        "lib" => ["responser", "exception"]
    ];
        
    Routing::model(null, $models);
    Routing::waf('auth/server');
    Routing::waf('waf');

    $config = Routing::config('auth');
    $responser = new Responser();
    $authServer = new AuthServer($entityManager);
    
    $entityManager = Routing::entityManager();

    $sid = 1;
    
    if(is_bool($entityManager)){
        $responser->toHttpRequest(500, "Invalid entity manager", null);
        die();
    }

    require_once ROOT .'/endpoints/core.php'; // <-- @sid @entityManager <--
    
    try{
        if($_SERVER["REQUEST_METHOD"] == 'POST'){
            if(!$guest){
                $responser->toHttpRequest(400, "Session already exists", null);
                die();
            }
        
            $user = $authServer->authenticate($data["username"], $data["password"]);
            if($user->getId() > 0){
                $authServer->sessionize($user, $data["persist"]);
                $responser->toHttpRequest(200, "Session established", null);
            }
        }
        if($_SERVER["REQUEST_METHOD"] == 'DELETE'){
            if($guest){
                $responser->toHttpRequest(404, "No sessions for delete", null);
                die();
            }
            if($authServer->unlink($sessionToken)){
                $responser->toHttpRequest(200, "Success logout", null);
                die();
            }
            $responser->toHttpRequest(400, "Logout error", null);
            die();
        }
    }
    catch(Exception $e){
        //$responser->toHttpRequest($e->getCode(), $e->getMessage(), null);
        if($e->getMessage() == "Password incorrect" || $e->getMessage() == "User not found"){
            $waf = new Waf($entityManager);
            $message = $waf->authFail($service);  
            $responser->toHttpRequest(401, $message, null);
        }
        if($e->getCode() >= 401 && $e->getCode() <= 403){
            //$responser->toHttpRequest(401, $e->getMessage, null);
            Routing::view(null, 'error/unauthorized.php?reason= '.$e->getCode(), true);
        }
    }
?>
   