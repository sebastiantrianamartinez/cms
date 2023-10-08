<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $modules = [
        "auth" => [
            "authentication",
            "authorization"
        ],
        "lib" => "responser",
        "log" => "reporter",
        "database" => [
            "database",
            "queryBuilder"
        ]
    ];

    routing::bigRouting($modules);
    
    $api_keys = array();

    if(isset($services)){
        foreach($services as $service){
            $headerRequest = execHeader($service);
            $key = $headerRequest["key"];
            if(is_string($key)){
                $api_keys[$service["id"]] = $key;
            }
        }
    }
    else{
        $headerRequest = execHeader($service);
        $key = $headerRequest["key"];
        if(is_string($key)){
            $api_keys[$service["id"]] = $key;
        }
    }
    $user = $headerRequest["user"];
    
    echo '<script> var api_keys = []; ';
            foreach($api_keys as $key => $value){
                echo 'api_keys[' .$key .'] = "' .$value .'";';
            }
    echo '</script>';

    $website = routing::config('project', 'dns')["data"]["website"];



    function execHeader($_service){
        $reporter = new reporter();
        $authentication = new authentication();
        $sessionRequest = $authentication->readSession();
        if($sessionRequest["status"] == 200){
            $user = $sessionRequest["data"]["payload"];
            $tokenId = $sessionRequest["data"]["token_id"];
        }
        else{
            $user = routing::config('auth', 'user')["data"];
            $tokenId = null;
        }
        $authorization = new authorization();
        $authorizationRequest = $authorization->permission($user, $_service);
        if($authorizationRequest["status"] == 200){
            $reporter->newLog(1, $user["id"], $service["id"], NULL);
            return [
                "key" => $authorizationRequest["data"],
                "user" => $user
            ];
        }
        else{
            $website = routing::config('project', 'dns')["data"]["website"];
            session_start();
            $_SESSION["referal"] = 'https://' .$_SERVER["HTTP_HOST"] .$_SERVER["REQUEST_URI"];
            if($tokenId != null){
                $reporter->newLog(10, $user["id"], $service["id"], NULL);
                header('location: ' .$website .'/error/401');
            }
            else{
                $reporter->newLog(9, $user["id"], $service["id"], NULL);
                header('location: ' .$website .'/login');
            }
        }
    }
