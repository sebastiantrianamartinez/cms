<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $service = [
        "id" => 1
    ];
    require_once ROOT .'/auth/headers/api_header.php';

    $modules = [
        "firewall" => "secureLogin"
    ];

    routing::bigRouting($modules);

    if(is_int($user["id"]) && $user["id"] > 0){
        responser::httpResponse(400, "A session already exists", NULL);
        die();
    }
    
    if($_SERVER["REQUEST_METHOD"] == 'POST'){
        $qb = new queryBuilder();
        $database = new database(true, NULL);
        $secureLogin = new secureLogin();

        $queryRequest = $qb->select("*")
            ->from('users')
            ->where('user_name', '=', $requestData["user_name"])
            ->orWhere('user_mail', '=', $requestData["user_name"])
            ->build();
        $selectQuery = $queryRequest["query"];
        $params = $queryRequest["params"];
        $userRequest = $database->executeQuery($selectQuery, $params)["data"];
        $userRequest = $userRequest->fetch();
        if(is_int($userRequest["user_id"])){
            if(password_verify($requestData["user_password"], $userRequest["user_password"])){
                $authentication->newSession($userRequest["user_id"], boolval($requestData["extended"]));
                session_start();
                if(isset($_SESSION["referal"])){
                    $redirect = $_SESSION["referal"];
                }
                else{
                    $redirect = "root";
                }
                unset($_SESSION['referal']);
                responser::httpResponse(200, "Sucess login, redirecting...", ["redirect" => $redirect]);
            }
            else{
                $secureRequest = $secureLogin->failedAttempt();
                responser::preformedHttpResponse($secureRequest);
            }
        }
        else{
            $secureRequest = $secureLogin->failedAttempt();
            responser::preformedHttpResponse($secureRequest);
        }
    }