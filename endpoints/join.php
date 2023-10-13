<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $service = [
        "id" => 3
    ];
    
    require_once ROOT .'/auth/headers/api_header.php';

    $modules = [
        "firewall" => "secureLogin",
        "database" => [
            "queryBuilder",
            "database"
        ],
        "lib" => "stringUtility"
    ];

    routing::bigRouting($modules);

    if(is_int($user["id"]) && $user["id"] > 0){
        responser::httpResponse(400, "A session already exists", NULL);
        die();
    }
    if($_SERVER["REQUEST_METHOD"] == 'PUT'){
        if(!is_string($requestData["user_name"])){
            responser::httpResponse(400, "Inoming data error", NULL);
            die();
        }
        $qb = new queryBuilder();
        $database = new database(true, NULL);
        $queryRequest = $qb->select('*')
            ->from('users')
            ->where('user_name', '=' , $requestData["user_name"])
            ->build();
        $selectRequest = $database->executeQuery($queryRequest["query"], $queryRequest["params"]);
        if($selectRequest["status"] != 200){
            responser::httpResponse(400, "Server error", NULL);
            die();
        }
        $matches = $selectRequest["data"]->fetchAll(PDO::FETCH_ASSOC);
        if(empty($matches)){
            responser::httpResponse(200, "User is available", NULL);
            die();
        }
        else{
            responser::httpResponse(400, "User is not available", NULL);
            die();
        }
    }
    if($_SERVER["REQUEST_METHOD"] == 'POST'){
        $qb = new queryBuilder();
        $database = new database(true, NULL);

        if($requestData["user_password"] !== $requestData["user_password_confirm"]){
            return responser::httpResponse(400, "Passwords don't match", NULL);
            die();
        }
        $dataValidity = intval(stringUtility::isStringValid($requestData["user_name"], stringUtility::LETTERS_AND_NUMBERS, ['-', '.'], 4));
        $dataValidity *= intval(stringUtility::isStringValid($requestData["user_alias"], stringUtility::LETTERS_AND_NUMBERS, [' ']), 1);
        $dataValidity *= intval(stringUtility::isStringValid($requestData["user_mail"], stringUtility::EMAIL_ADDRESS, NULL), 4);
        $dataValidity *= intval(stringUtility::isStringLengthValid($requestData["user_password"], 8, 100));

        if($dataValidity === 0){
            return responser::httpResponse(400, "Data is incorrect", NULL);
            die();
        }
        $insertData = [
            "user_name" => $requestData["user_name"],
            "user_mail" => $requestData["user_mail"],
            "user_password" => password_hash($requestData["user_password"], PASSWORD_BCRYPT),
            "user_alias" => $requestData["user_alias"],
        ];
        $queryRequest = $qb->insert('users', $insertData)
            ->build();
        $insertRequest = $database->executeQuery($queryRequest["query"], $queryRequest["params"]);
        if($insertRequest["status"] != 200){
            if(strpos($insertRequest["data"], "user_mail")){
                return responser::httpResponse(200, "Invalid mail " , NULL);
            }
            if(strpos($insertRequest["data"], "user_name")){
                return responser::httpResponse(200, "Invalid username " , NULL);
            }
            return responser::httpResponse(200, "Database  fatal error ".$insertRequest["data"], NULL);
        }
        return responser::httpResponse(200, "Success user registration", NULL);
    }