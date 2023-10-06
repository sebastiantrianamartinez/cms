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
        ],
        "firewall" => "middleware"
    ];

    routing::bigRouting($modules);

    $reporter = new reporter();

    $headers = getallheaders();
    if(isset($headers['Authorization'])){
        $authHeader = $headers['Authorization'];
        $headerParts = explode(" ", $authHeader);
        if(count($headerParts) === 2 && $headerParts[0] === 'Bearer'){
            $api_key = $headerParts[1];
        }
    }

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
    $authorizationRequest = $authorization->transaction($user, $service, $api_key);
    if($authorizationRequest["status"] != 200){
        $code = $authorizationRequest["data"];
        $errors = routing::config('auth', 'error')["data"]["authorization"];
        responser::preformedHttpResponse($authorizationRequest);
        die();
        if(array_key_exists($code, $errors)){
            responser::httpResponse(401, $errors[$code]["message"], ["details" => $errors[$code]["details"]]);
            $reporter->newLog(10, $user["id"], $service["id"], $code);
            die();
        }
        else{
            responser::httpResponse(401, "Server error", NULL);
            $reporter->newLog(10, $user["id"], $service["id"], "uknown error");
            die();
        }
    }

    $requestData = json_decode(file_get_contents('php://input'), true);
    $requestData = (isset($requestData)) ? $requestData : $_POST;

    $middleware = new middleware();
    $middlewareRequest = $middleware->sanitizeArrayRecursive($requestData);
    if($middlewareRequest["status"] != 200){
        $reporter->newLog(10, $user["id"], $service["id"], "INJ");
        echo responser::httpResponse(400, "Request violates security policy, be careful", NULL);
        die();
    }
    $reporter->newLog(14, $user["id"], $service["id"], $code);
